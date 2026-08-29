<?php

declare(strict_types=1);

use Capell\Admin\Data\Bridges\AdminBridgeContextData;
use Capell\Admin\Enums\DashboardEnum;
use Capell\Admin\Facades\CapellAdmin;
use Capell\Admin\Support\Bridges\AdminBridgeRegistrar;
use Capell\Core\Enums\ExtensionContributionType;
use Capell\ExtensionCookbook\Admin\Workflow\ExtensionCookbookWorkflowAttentionContribution;
use Capell\ExtensionCookbook\Bridges\ExtensionCookbookAdminBridge;
use Capell\ExtensionCookbook\Filament\Configurators\ExtensionCookbookConfigurator;
use Capell\ExtensionCookbook\Filament\Extenders\ExtensionCookbookAdminActionExtender;
use Capell\ExtensionCookbook\Filament\Extenders\ExtensionCookbookPageSchemaExtender;
use Capell\ExtensionCookbook\Filament\Pages\ExtensionCookbookPage;
use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\Pages\ListReferenceEntries;
use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\ReferenceEntryResource;
use Capell\ExtensionCookbook\Filament\Widgets\ExtensionCookbookDashboardWidget;
use Capell\ExtensionCookbook\Filament\Widgets\ExtensionCookbookOverviewStat;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookAdminActionExtenderContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookAdminPageContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookConfiguratorContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookDashboardWidgetContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookOverviewStatContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookPermissionsContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookSchemaExtenderContribution;
use Capell\ExtensionCookbook\Manifest\Admin\ReferenceEntryResourceContribution;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Capell\ExtensionCookbook\Policies\ReferenceEntryPolicy;
use Capell\ExtensionCookbook\Support\Admin\ExtensionCookbookCoverageCatalogue;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

it('declares the complete admin surface with translation-backed labels', function (): void {
    expect(ExtensionCookbookPage::getNavigationLabel())->toBe('Capell Extension Cookbook')
        ->and(ReferenceEntryResource::getNavigationLabel())->toBe('Reference entries')
        ->and(ReferenceEntryResource::getNavigationGroup())->toBe('System')
        ->and(ReferenceEntryResource::getPages())->toHaveKey('index')
        ->and(ExtensionCookbookConfigurator::getKey())->toBe('extension-cookbook');
});

it('registers the resource, page, widgets, configurator and schema extender through the bridge', function (): void {
    CapellAdmin::clearAdminSurfaceContributions();

    (new ExtensionCookbookAdminBridge)->register(
        resolve(AdminBridgeRegistrar::class),
        AdminBridgeContextData::forPackage('capell-app/extension-cookbook'),
    );

    $all = CapellAdmin::getAdminSurfaceContributions();
    $classes = collect($all)->flatten(1)->pluck('class')->all();

    expect($classes)->toContain(
        ReferenceEntryResource::class,
        ExtensionCookbookPage::class,
        ExtensionCookbookConfigurator::class,
        ExtensionCookbookPageSchemaExtender::class,
    );

    expect(CapellAdmin::getDashboardFilamentWidgets(DashboardEnum::Extensions))
        ->toContain(ExtensionCookbookDashboardWidget::class)
        ->and(CapellAdmin::getDashboardFilamentWidgets(DashboardEnum::Main))
        ->toContain(ExtensionCookbookOverviewStat::class);
});

it('keeps the catalogue typed and complete across the three admin groups', function (): void {
    $catalogue = ExtensionCookbookCoverageCatalogue::entries();
    $expectedTypes = collect(ExtensionContributionType::cases())
        ->map(static fn (ExtensionContributionType $type): string => $type->value)
        ->sort()
        ->values()
        ->all();
    $catalogueTypes = collect($catalogue)
        ->flatten(1)
        ->pluck('type')
        ->sort()
        ->values()
        ->all();

    expect($catalogue)->toHaveKeys(['Core', 'Admin', 'Frontend'])
        ->and(collect($catalogue)->flatten(1))->toHaveCount(27)
        ->and($catalogueTypes)->toBe($expectedTypes)
        ->and(collect($catalogue['Admin'])->pluck('type'))->toContain('admin-resource', 'admin-page', 'configurator', 'permission')
        ->and(collect($catalogue)->flatten(1)->keyBy('type')->only([
            'section',
            'page-variation',
            'agent-capability',
        ])->pluck('status')->unique()->sort()->values()->all())->toBe([
            'Deliberately inactive',
        ])
        ->and(collect($catalogue)->flatten(1)->keyBy('type')->except([
            'section',
            'page-variation',
            'agent-capability',
        ])->pluck('status')->unique()->all())->toBe([
            'Demonstrated',
        ])
        ->and(collect($catalogue)->flatten(1)->every(fn (array $entry): bool => isset($entry['type'], $entry['status'], $entry['purpose'])))->toBeTrue();
});

it('keeps registrar coverage separate and marks global replacement seams inactive', function (): void {
    $registrarCoverage = ExtensionCookbookCoverageCatalogue::registrarCoverage();

    expect($registrarCoverage)->toHaveKeys(['safe', 'inactive'])
        ->and(collect($registrarCoverage['safe'])->pluck('status')->unique()->all())->toBe(['Demonstrated'])
        ->and(collect($registrarCoverage['inactive'])->pluck('method')->all())->toBe([
            'dashboardPage',
            'extensionRemovalCoordinator',
            'pendingThemeInstallProvider',
            'settingsSchema',
        ])
        ->and(collect($registrarCoverage['inactive'])->pluck('status')->unique()->all())->toBe(['Deliberately inactive']);
});

it('returns no workflow attention item before an actor is supplied', function (): void {
    expect((new ExtensionCookbookWorkflowAttentionContribution)->attentionItems())->toBe([]);
});

it('provides a safe page action only for the reference entries resource', function (): void {
    $extender = new ExtensionCookbookAdminActionExtender;
    $actions = $extender->actions();

    expect($extender->supports(ListReferenceEntries::class))->toBeTrue()
        ->and($extender->supports(ReferenceEntryResource::class))->toBeFalse()
        ->and($extender->supports(ExtensionCookbookPage::class))->toBeFalse()
        ->and($actions)->toHaveCount(1);
});

it('uses the declared extension cookbook permission for admin pages, widgets and policy abilities', function (): void {
    $user = new class implements Authenticatable
    {
        /** @var Collection<int, int> */
        public Collection $assignedSiteIds;

        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return 1;
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getRememberToken(): string
        {
            return '';
        }

        public function setRememberToken($value): void {}

        public function getRememberTokenName(): string
        {
            return 'remember_token';
        }

        /** @return Collection<int, int> */
        public function getAssignedSiteIds(): Collection
        {
            return $this->assignedSiteIds;
        }
    };

    $user->assignedSiteIds = collect([101]);

    Gate::define('View:ExtensionCookbook', static fn (Authenticatable $actor): bool => $actor === $user);
    Gate::define('Manage:ExtensionCookbook', static fn (Authenticatable $actor): bool => $actor === $user);
    auth()->setUser($user);

    $policy = new ReferenceEntryPolicy;
    $entry = new ReferenceEntry(['site_id' => 101]);

    expect(ExtensionCookbookPage::canAccess())->toBeTrue()
        ->and(ExtensionCookbookDashboardWidget::canView())->toBeTrue()
        ->and(ExtensionCookbookOverviewStat::canView())->toBeTrue()
        ->and($policy->viewAny($user))->toBeTrue()
        ->and($policy->view($user, $entry))->toBeTrue()
        ->and($policy->create($user))->toBeTrue()
        ->and($policy->update($user, $entry))->toBeTrue()
        ->and($policy->delete($user, $entry))->toBeTrue();

    auth()->logout();

    expect(ExtensionCookbookPage::canAccess())->toBeFalse()
        ->and(ExtensionCookbookDashboardWidget::canView())->toBeFalse()
        ->and(ExtensionCookbookOverviewStat::canView())->toBeFalse();
});

it('scopes reference entries, aggregate stats, workflow attention and policy checks by site', function (): void {
    Schema::dropIfExists('extension_cookbook_entries');
    Schema::create('extension_cookbook_entries', function (Blueprint $table): void {
        $table->id();
        $table->unsignedBigInteger('site_id');
        $table->string('title');
        $table->string('slug');
        $table->boolean('enabled')->default(true);
        $table->timestamps();
    });

    DB::table('extension_cookbook_entries')->insert([
        ['site_id' => 101, 'title' => 'Assigned enabled', 'slug' => 'assigned-enabled', 'enabled' => true],
        ['site_id' => 101, 'title' => 'Assigned disabled', 'slug' => 'assigned-disabled', 'enabled' => false],
        ['site_id' => 202, 'title' => 'Other enabled', 'slug' => 'other-enabled', 'enabled' => true],
    ]);

    $user = new class implements Authenticatable
    {
        /** @var Collection<int, int> */
        public Collection $assignedSiteIds;

        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return 1;
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getRememberToken(): string
        {
            return '';
        }

        public function setRememberToken($value): void {}

        public function getRememberTokenName(): string
        {
            return 'remember_token';
        }

        /** @return Collection<int, int> */
        public function getAssignedSiteIds(): Collection
        {
            return $this->assignedSiteIds;
        }
    };
    $user->assignedSiteIds = collect([101]);

    Gate::define('View:ExtensionCookbook', static fn (Authenticatable $actor): bool => $actor === $user);
    Gate::define('Manage:ExtensionCookbook', static fn (Authenticatable $actor): bool => $actor === $user);
    auth()->setUser($user);

    $statsMethod = new ReflectionMethod(ExtensionCookbookOverviewStat::class, 'getStats');
    $statsResult = $statsMethod->invoke(new ExtensionCookbookOverviewStat);

    if (! is_array($statsResult)) {
        throw new RuntimeException('Expected an array of extension cookbook statistics.');
    }

    $stats = $statsResult;
    $stat = $stats[0] ?? null;

    if (! $stat instanceof Stat) {
        throw new RuntimeException('Expected a typed extension cookbook statistic.');
    }
    $workflow = new ExtensionCookbookWorkflowAttentionContribution;
    $policy = new ReferenceEntryPolicy;
    $assignedEntry = new ReferenceEntry(['site_id' => 101]);
    $otherEntry = new ReferenceEntry(['site_id' => 202]);
    $unassignedEntry = new ReferenceEntry;

    expect(ReferenceEntryResource::getEloquentQuery()->pluck('id')->all())->toEqualCanonicalizing([1, 2])
        ->and($stat->getValue())->toBe('1/2')
        ->and($workflow->attentionItems($user))->toBe([])
        ->and($policy->view($user, $assignedEntry))->toBeTrue()
        ->and($policy->view($user, $otherEntry))->toBeFalse()
        ->and($policy->view($user, $unassignedEntry))->toBeFalse()
        ->and($policy->update($user, $assignedEntry))->toBeTrue()
        ->and($policy->delete($user, $otherEntry))->toBeFalse();

    DB::table('extension_cookbook_entries')->where('site_id', 101)->update(['enabled' => false]);

    expect($workflow->attentionItems($user))->toHaveCount(1);

    auth()->logout();
    Schema::dropIfExists('extension_cookbook_entries');
});

it('does not activate dangerous global replacement seams', function (): void {
    $bridgeSource = file_get_contents(dirname(__DIR__, 3) . '/src/Bridges/ExtensionCookbookAdminBridge.php');

    expect($bridgeSource)->toBeString()
        ->and($bridgeSource)->not->toContain('dashboardPage(')
        ->and($bridgeSource)->not->toContain('extensionRemovalCoordinator(')
        ->and($bridgeSource)->not->toContain('pendingThemeInstallProvider(')
        ->and($bridgeSource)->not->toContain('settingsSchema(');
});

it('fails closed when workflow attention runs before its table exists', function (): void {
    Schema::dropIfExists('extension_cookbook_entries');
    $user = Mockery::mock(Authenticatable::class);

    Gate::define('View:ExtensionCookbook', static fn (Authenticatable $actor): bool => $actor === $user);

    expect((new ExtensionCookbookWorkflowAttentionContribution)->attentionItems($user))->toBe([]);
});

it('keeps all declared admin contribution classes on the public API contract', function (): void {
    foreach ([
        ExtensionCookbookAdminPageContribution::class,
        ReferenceEntryResourceContribution::class,
        ExtensionCookbookAdminActionExtenderContribution::class,
        ExtensionCookbookDashboardWidgetContribution::class,
        ExtensionCookbookOverviewStatContribution::class,
        ExtensionCookbookSchemaExtenderContribution::class,
        ExtensionCookbookConfiguratorContribution::class,
        ExtensionCookbookPermissionsContribution::class,
    ] as $contribution) {
        expect($contribution::compatibleCapellApiVersion())->toBe('^1.0');
    }
});
