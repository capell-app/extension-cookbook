<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Core;

use Capell\Core\Contracts\Extensions\RegistersExtensionOutboundEvent;

final class ExtensionCookbookOutboundEventContribution implements RegistersExtensionOutboundEvent
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
