<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Core;

use Capell\Core\Contracts\Extensions\RegistersExtensionBlueprintSubject;

final class ExtensionCookbookBlueprintSubjectContribution implements RegistersExtensionBlueprintSubject
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
