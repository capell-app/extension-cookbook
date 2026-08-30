<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Widget;

use Spatie\LaravelData\Data;

final class ExtensionCookbookWidgetInputData extends Data
{
    public function __construct(public string $title = 'Extension examples', public string $summary = 'A practical reference for extension contracts.') {}
}
