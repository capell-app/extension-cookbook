<?php

declare(strict_types=1);

use Capell\Core\Data\Manifest\ExtensionProviderData;
use Capell\Core\Data\Runtime\RuntimeRoleSelectionData;
use Capell\Core\Enums\RuntimeRole;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Manifest\CapellManifestData;
use Capell\Core\Support\Manifest\ManifestValidator;
use Capell\Core\Support\PackageRegistry\CapellPackageLoader;
use Capell\Core\Support\PackageRegistry\CapellPackageRegistry;
use Capell\Core\Support\Runtime\RuntimeRoleProviderPolicy;
use Capell\Core\Support\Runtime\RuntimeRoleResolver;
use Illuminate\Contracts\Foundation\Application;
use Mockery\MockInterface;

it('declares every extension cookbook contribution on the documented Core contract', function (): void {
    $packageRoot = dirname(__DIR__, 3);
    $readJsonObject = static function (string $path): array {
        $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new RuntimeException('Expected a JSON object.');
        }

        $object = [];
        foreach ($decoded as $key => $value) {
            if (! is_string($key)) {
                throw new RuntimeException('Expected string JSON object keys.');
            }

            $object[$key] = $value;
        }

        return $object;
    };

    $manifest = $readJsonObject($packageRoot . '/capell.json');
    $composer = $readJsonObject($packageRoot . '/composer.json');
    $screenshots = $readJsonObject($packageRoot . '/docs/screenshots.json');

    expect($manifest['contributes'])->toHaveCount(27);
    expect($screenshots['generatedFor'] ?? null)->toBe('shared-capell-screenshot-runner')
        ->and($screenshots['provenancePolicy'] ?? null)->toBe('runner-only-v1');

    expect(function () use ($manifest, $composer): void {
        (new ManifestValidator)->validate(
            $manifest,
            $composer,
            'capell-app/extension-cookbook',
            'packages/extension-cookbook/capell.json',
        );
    })->not->toThrow(Throwable::class);
});

it('keeps provider buckets aligned with the runtime-role contract', function (): void {
    $path = dirname(__DIR__, 3) . '/capell.json';
    $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

    if (! is_array($decoded) || ! is_array($decoded['providers'] ?? null)) {
        throw new RuntimeException('Expected manifest provider buckets.');
    }

    $providerData = [];
    foreach ($decoded['providers'] as $key => $value) {
        if (! is_string($key)) {
            throw new RuntimeException('Expected string provider bucket keys.');
        }

        $providerData[$key] = $value;
    }

    $providers = ExtensionProviderData::fromArray($providerData);
    $policy = new RuntimeRoleProviderPolicy;

    expect($policy->extensionProviders($providers, RuntimeRole::Public))->toBe([
        'Capell\\ExtensionCookbook\\Providers\\ExtensionCookbookServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\FrontendServiceProvider',
    ])->and($policy->extensionProviders($providers, RuntimeRole::Combined))->toBe([
        'Capell\\ExtensionCookbook\\Providers\\ExtensionCookbookServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\AdminServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\FrontendServiceProvider',
    ]);
});

it('loads the enabled package through the installed runtime-role provider matrix', function (): void {
    $manifest = json_decode(
        (string) file_get_contents(dirname(__DIR__, 3) . '/capell.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    if (! is_array($manifest)) {
        throw new RuntimeException('Expected a package manifest object.');
    }

    $manifestObject = [];
    foreach ($manifest as $key => $value) {
        if (! is_string($key)) {
            throw new RuntimeException('Expected string JSON object keys.');
        }

        $manifestObject[$key] = $value;
    }

    $registry = new CapellPackageRegistry;
    $registry->register(CapellManifestData::fromArray($manifestObject));

    CapellCore::shouldReceive('isPackageEnabled')
        ->twice()
        ->with('capell-app/extension-cookbook')
        ->andReturnTrue();

    /** @var Application&MockInterface $application */
    $application = Mockery::mock(Application::class);
    $publicLoader = new CapellPackageLoader(
        $application,
        $registry,
        runtimeRoleResolver: new RuntimeRoleResolver(new RuntimeRoleSelectionData(
            role: RuntimeRole::Public,
            configuredValue: RuntimeRole::Public->value,
            valid: true,
        )),
    );
    $authoringLoader = new CapellPackageLoader(
        $application,
        $registry,
        runtimeRoleResolver: new RuntimeRoleResolver(new RuntimeRoleSelectionData(
            role: RuntimeRole::Combined,
            configuredValue: RuntimeRole::Combined->value,
            valid: true,
        )),
    );

    expect($publicLoader->collectProviders())->toBe([
        'Capell\\ExtensionCookbook\\Providers\\ExtensionCookbookServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\FrontendServiceProvider',
    ])->and($authoringLoader->collectProviders())->toBe([
        'Capell\\ExtensionCookbook\\Providers\\ExtensionCookbookServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\AdminServiceProvider',
        'Capell\\ExtensionCookbook\\Providers\\FrontendServiceProvider',
    ]);
});

it('loads no runtime providers for a disabled package', function (): void {
    $manifest = json_decode(
        (string) file_get_contents(dirname(__DIR__, 3) . '/capell.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );

    if (! is_array($manifest)) {
        throw new RuntimeException('Expected a package manifest object.');
    }

    $manifestObject = [];
    foreach ($manifest as $key => $value) {
        if (! is_string($key)) {
            throw new RuntimeException('Expected string JSON object keys.');
        }

        $manifestObject[$key] = $value;
    }

    $registry = new CapellPackageRegistry;
    $registry->register(CapellManifestData::fromArray($manifestObject));

    CapellCore::shouldReceive('isPackageEnabled')
        ->once()
        ->with('capell-app/extension-cookbook')
        ->andReturnFalse();

    /** @var Application&MockInterface $application */
    $application = Mockery::mock(Application::class);

    expect(new CapellPackageLoader(
        $application,
        $registry,
        runtimeRoleResolver: new RuntimeRoleResolver(new RuntimeRoleSelectionData(
            role: RuntimeRole::Public,
            configuredValue: RuntimeRole::Public->value,
            valid: true,
        )),
    )->collectProviders())->toBe([]);
});
