<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookFrontendComponentContributor;
use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookRenderHook;
use Capell\Frontend\Contracts\FrontendContextReader;
use Capell\Frontend\Data\RenderHookContext;
use Capell\Frontend\Enums\FrontendComponentTarget;
use Capell\Frontend\Enums\RenderHookLocation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

it('renders the public view from hydrated values without querying or exposing internals', function (): void {
    View::addNamespace('capell-extension-cookbook', dirname(__DIR__, 3) . '/resources/views');
    $queries = 0;
    DB::listen(static function (QueryExecuted $event) use (&$queries): void {
        $queries++;
    });

    $html = view('capell-extension-cookbook::frontend.index', [
        'examples' => ['A public route', 'A typed widget'],
    ])->render();

    expect($queries)->toBe(0)
        ->and($html)->toContain('A public route', 'A typed widget')
        ->not->toContain('data-showcase', 'wire:', '__capell', 'field_path', 'signed');
});

it('renders the hook without package selectors or authoring state', function (): void {
    $html = (new ExtensionCookbookRenderHook)->render(new RenderHookContext(RenderHookLocation::BodyEnd->value, null));

    expect($html)->not->toContain('data-showcase', 'wire:', '__capell', 'field_path', 'signed');
});

it('declares a named public route owned by the package', function (): void {
    require dirname(__DIR__, 3) . '/routes/web.php';
    Route::getRoutes()->refreshNameLookups();

    expect(Route::getRoutes()->getByName('capell-extension-cookbook.index'))->not->toBeNull();
});

it('serves the real public route without exposing package or authoring internals', function (): void {
    require dirname(__DIR__, 3) . '/routes/web.php';
    app()->instance(FrontendContextReader::class, Mockery::mock(FrontendContextReader::class, ['site' => null]));

    $response = $this->get('/extension-cookbook');
    $body = (string) $response->getContent();

    $response->assertSuccessful();

    expect($body)->toContain('Extension examples')
        ->not->toContain(
            'capell-app/extension-cookbook',
            'capell-app.extension-cookbook',
            'capell-extension-cookbook',
            'extension_cookbook',
            'Capell\\ExtensionCookbook',
            'ExtensionCookbook',
            'ReferenceEntry',
            'reference_entry',
            'View:ExtensionCookbook',
            'Manage:ExtensionCookbook',
            'site_id',
            'related_page_id',
            'blueprint_id',
            'wire:',
            '__capell',
            'field_path',
            'signed',
        );
});

it('contributes the public Blade component through the typed frontend contract', function (): void {
    $contributions = (new ExtensionCookbookFrontendComponentContributor)->components();

    expect($contributions)->toHaveCount(1)
        ->and($contributions[0]->name)->toBe('extension-cookbook.widget')
        ->and($contributions[0]->component)->toBe('capell-extension-cookbook::widget.extension-cookbook')
        ->and($contributions[0]->target)->toBe(FrontendComponentTarget::Blade);
});
