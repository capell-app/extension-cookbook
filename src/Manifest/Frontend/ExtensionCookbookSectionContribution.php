<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\RegistersExtensionSection;

final class ExtensionCookbookSectionContribution implements RegistersExtensionSection
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
