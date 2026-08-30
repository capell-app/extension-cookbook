<?php

declare(strict_types=1);

use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;

it('returns a bounded typed storage diagnostic', function (): void {
    $results = ExtensionCookbookHealthCheck::runDiagnostics();
    $result = $results->first();

    if (! $result instanceof DoctorCheckResultData) {
        throw new RuntimeException('Expected one typed health-check result.');
    }

    expect($results)->toHaveCount(1)
        ->and($result->label)->toBe('Capell Extension Cookbook storage');
});
