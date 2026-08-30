<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Data;

use Spatie\LaravelData\Data;

final class ReferenceEntryData extends Data
{
    /** @param array<string, mixed> $metadata */
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $summary = null,
        public readonly ?string $body = null,
        public readonly bool $enabled = true,
        public readonly ?int $siteId = null,
        public readonly ?int $relatedPageId = null,
        public readonly ?int $blueprintId = null,
        public readonly array $metadata = [],
    ) {}
}
