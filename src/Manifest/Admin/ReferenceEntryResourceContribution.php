<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Admin;

use Capell\Core\Contracts\Extensions\ExtensionContribution;
use Capell\Core\Contracts\Extensions\RegistersExtensionAdminResource;

final class ReferenceEntryResourceContribution implements ExtensionContribution, RegistersExtensionAdminResource
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
