<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Core\Support\Migration\MigrationRootRegistry;
use Illuminate\Support\ServiceProvider;
use Override;

final class MigrationMetadataServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {

        $this->callAfterResolving(MigrationRootRegistry::class, static function (MigrationRootRegistry $registry): void {
            $registry->register('capell-app/extension-cookbook', __DIR__ . '/../../database/migrations', [
                '2026_08_29_000001_create_extension_cookbook_entries' => 'extension_cookbook_entries',
                '2026_11_14_000001_create_extension_cookbook_guides' => 'extension_cookbook_guides',
            ]);
        });
    }
}
