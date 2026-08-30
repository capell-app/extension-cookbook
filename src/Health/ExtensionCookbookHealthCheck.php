<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Health;

use Capell\Core\Contracts\Extensions\ChecksExtensionHealth;
use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class ExtensionCookbookHealthCheck implements ChecksExtensionHealth
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    /** @return Collection<int, DoctorCheckResultData> */
    public static function runDiagnostics(): Collection
    {
        $table = (new ReferenceEntry)->getTable();
        $available = Schema::hasTable($table);

        return collect([new DoctorCheckResultData(
            label: 'Capell Extension Cookbook storage',
            passed: $available,
            message: $available ? 'Extension Cookbook storage is available.' : 'Extension Cookbook storage table is missing.',
            remediation: $available ? null : 'Run the Capell Extension Cookbook migrations.',
        )]);
    }

    public static function passed(): bool
    {
        return self::runDiagnostics()->every(static fn (DoctorCheckResultData $result): bool => $result->passed);
    }
}
