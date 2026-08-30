<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Data;

use Spatie\LaravelData\Data;

final class ReferenceEntryPublishedPayloadData extends Data
{
    public function __construct(public readonly string $slug, public readonly string $title) {}
}
