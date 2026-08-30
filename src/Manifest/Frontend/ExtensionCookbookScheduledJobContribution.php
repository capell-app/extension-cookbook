<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Manifest\Frontend;

use Capell\Core\Contracts\Extensions\RunsScheduledExtensionJob;

final class ExtensionCookbookScheduledJobContribution implements RunsScheduledExtensionJob
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }
}
