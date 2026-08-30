<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Console\Commands;

use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;
use Illuminate\Console\Command;

final class ExtensionCookbookDoctorCommand extends Command
{
    protected $signature = 'capell:extension-cookbook-doctor';

    protected $description = 'Check Capell Extension Cookbook availability.';

    public function handle(): int
    {
        $checks = ExtensionCookbookHealthCheck::runDiagnostics();

        foreach ($checks as $check) {
            $check->passed ? $this->info($check->message) : $this->error($check->message);
        }

        return $checks->every(static fn (DoctorCheckResultData $check): bool => $check->passed)
            ? self::SUCCESS
            : self::FAILURE;
    }
}
