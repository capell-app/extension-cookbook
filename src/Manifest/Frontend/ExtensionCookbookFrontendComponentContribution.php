<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\RegistersExtensionFrontendComponent;

final class ExtensionCookbookFrontendComponentContribution implements RegistersExtensionFrontendComponent
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
