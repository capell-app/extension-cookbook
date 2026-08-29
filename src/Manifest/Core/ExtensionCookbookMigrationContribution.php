<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Core;

use Capell\Core\Contracts\Extensions\RunsExtensionMigration;

final class ExtensionCookbookMigrationContribution implements RunsExtensionMigration
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
