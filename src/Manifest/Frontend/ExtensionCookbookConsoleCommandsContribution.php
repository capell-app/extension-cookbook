<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\ExtensionContribution;

final class ExtensionCookbookConsoleCommandsContribution implements ExtensionContribution
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
