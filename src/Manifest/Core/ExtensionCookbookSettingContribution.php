<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Core;

use Capell\Core\Contracts\Extensions\RegistersExtensionSetting;

final class ExtensionCookbookSettingContribution implements RegistersExtensionSetting
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
