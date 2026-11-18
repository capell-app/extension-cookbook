<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Admin;

use Capell\Core\Contracts\Extensions\DeclaresPermissions;
use Capell\Core\Contracts\Extensions\ExtensionContribution;
use Capell\Core\Contracts\Extensions\RegistersExtensionPermission;
use Capell\Core\Data\Permissions\PermissionData;
use Capell\Core\Data\Permissions\PermissionSetData;

/**
 * The package's permissions, typed. The keys match capell.json `permissions`, which stays the
 * static list Marketplace and install review read without loading code.
 */
final class ExtensionCookbookPermissionsContribution implements DeclaresPermissions, ExtensionContribution, RegistersExtensionPermission
{
    /** @var list<string> */
    public const array KEYS = [
        'View:ExtensionCookbook',
        'Manage:ExtensionCookbook',
    ];

    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    public function permissions(): PermissionSetData
    {
        return new PermissionSetData(array_map(
            static fn (string $key): PermissionData => new PermissionData(
                key: $key,
                label: (string) __('capell-extension-cookbook::permissions.labels.' . $key),
                group: (string) __('capell-extension-cookbook::permissions.groups.' . explode(':', $key, 2)[1]),
            ),
            self::KEYS,
        ));
    }
}
