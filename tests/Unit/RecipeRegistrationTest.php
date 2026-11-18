<?php

declare(strict_types=1);

use Capell\ContentSections\Contracts\SectionDefinitionProvider;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionCookbook\Bridges\ExtensionCookbookSectionDefinitionProvider;
use Capell\ExtensionCookbook\Models\ReferenceGuide;
use Capell\ExtensionCookbook\Providers\FrontendServiceProvider;
use Capell\Tests\Support\InstalledRuntimeParity;
use Capell\Tests\Support\InstalledRuntimeState;
use Capell\Tests\Support\InstalledRuntimeTestCase;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Relations\Relation;

/** @return list<class-string> */
function extensionCookbookTaggedSectionProviders(Application $app): array
{
    return array_values(array_map(
        static fn (object $provider): string => $provider::class,
        iterator_to_array($app->tagged(SectionDefinitionProvider::TAG), false),
    ));
}

it('registers the page-variation recipe while the cookbook is installed and enabled', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), true, sourceRoot: dirname(__DIR__, 2));

        expect(CapellCore::getPageVariation(FrontendServiceProvider::PAGE_VARIATION)?->model)->toBe(ReferenceGuide::class)
            ->and(Relation::getMorphedModel((new ReferenceGuide)->getMorphClass()))->toBe(ReferenceGuide::class);
    });
});

it('tags the section recipe only while Content Sections is available', function (bool $available): void {
    InstalledRuntimeState::run(static function () use ($available): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), true, sourceRoot: dirname(__DIR__, 2));
        $tagged = static fn (): array => extensionCookbookTaggedSectionProviders($app);
        CapellCore::partialMock()->shouldReceive('isPackageAvailable')->with('capell-app/content-sections')->andReturn($available);

        $before = $tagged();
        $provider = new FrontendServiceProvider($app);
        (new ReflectionMethod($provider, 'registerSectionRecipe'))->invoke($provider);

        expect(in_array(ExtensionCookbookSectionDefinitionProvider::class, $tagged(), true))->toBe($available)
            ->and(in_array(ExtensionCookbookSectionDefinitionProvider::class, $before, true))->toBeFalse();

        if ($available) {
            $definition = (new ExtensionCookbookSectionDefinitionProvider)->definitions()[0];
            expect($definition->key)->toBe(ExtensionCookbookSectionDefinitionProvider::KEY)
                ->and($definition->label)->not->toBe('')
                ->and(view()->exists($definition->component))->toBeTrue();
        }
    });
})->with([true, false]);

it('leaves both recipes unregistered while the cookbook is not installed and enabled', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), false, sourceRoot: dirname(__DIR__, 2));

        expect(extensionCookbookTaggedSectionProviders($app))->not->toContain(ExtensionCookbookSectionDefinitionProvider::class)
            ->and(CapellCore::hasPageVariation(FrontendServiceProvider::PAGE_VARIATION))->toBeFalse();
    });
});
