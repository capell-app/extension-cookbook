<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Admin;

use Capell\Core\Contracts\Extensions\ExtensionContribution;

final class ExtensionCookbookAdminActionExtenderContribution implements ExtensionContribution
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
