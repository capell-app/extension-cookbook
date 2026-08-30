<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Jobs;

use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class AuditExtensionCookbookHealthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function handle(): void
    {
        ExtensionCookbookHealthCheck::runDiagnostics();
    }
}
