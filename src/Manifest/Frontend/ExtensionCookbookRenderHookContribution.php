<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\RegistersExtensionRenderHook;

final class ExtensionCookbookRenderHookContribution implements RegistersExtensionRenderHook
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
