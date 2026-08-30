<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Admin;

use Capell\Core\Contracts\Extensions\ExtensionContribution;
use Capell\Core\Contracts\Extensions\RegistersExtensionPermission;

final class ExtensionCookbookPermissionsContribution implements ExtensionContribution, RegistersExtensionPermission
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
