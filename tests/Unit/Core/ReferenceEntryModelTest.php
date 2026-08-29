<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Models\ReferenceEntry;

it('defines the package-owned model shape and enabled scope', function (): void {
    $model = new ReferenceEntry;

    expect($model->getTable())->toBe('extension_cookbook_entries')
        ->and($model->getFillable())->toContain('title', 'slug', 'metadata')
        ->and($model->getCasts())->toMatchArray(['enabled' => 'bool', 'metadata' => 'array']);
});
