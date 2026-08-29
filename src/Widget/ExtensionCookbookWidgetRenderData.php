<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Widget;

use Spatie\LaravelData\Data;

final class ExtensionCookbookWidgetRenderData extends Data
{
    public function __construct(public readonly string $title, public readonly string $summary) {}

    public static function fromInput(ExtensionCookbookWidgetInputData $input): self
    {
        return new self($input->title, $input->summary);
    }
}
