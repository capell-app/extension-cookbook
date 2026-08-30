<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Jobs;

use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class AuditExtensionCookbookHealthJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function handle(): void
    {
        ExtensionCookbookHealthCheck::runDiagnostics();
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Extension Cookbook health audit job exhausted its queue retry window.', [
            'exception_class' => $exception instanceof Throwable ? $exception::class : null,
        ]);
    }
}
