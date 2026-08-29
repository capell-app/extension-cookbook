<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\RegistersExtensionAsset;

final class ExtensionCookbookAssetContribution implements RegistersExtensionAsset
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
