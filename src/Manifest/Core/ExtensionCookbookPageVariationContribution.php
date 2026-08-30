<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Core;

use Capell\Core\Contracts\Extensions\RegistersExtensionPageType;

final class ExtensionCookbookPageVariationContribution implements RegistersExtensionPageType
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
