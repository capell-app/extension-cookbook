<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Admin;

use Capell\Core\Contracts\Extensions\ExtensionContribution;
use Capell\Core\Contracts\Extensions\RegistersExtensionFilamentWidget;

final class ExtensionCookbookDashboardWidgetContribution implements ExtensionContribution, RegistersExtensionFilamentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
