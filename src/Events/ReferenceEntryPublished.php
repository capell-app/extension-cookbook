<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Events;

use Capell\ExtensionCookbook\Models\ReferenceEntry;

final class ReferenceEntryPublished
{
    public function __construct(public readonly ReferenceEntry $entry) {}
}
