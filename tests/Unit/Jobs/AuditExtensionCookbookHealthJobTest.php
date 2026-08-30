<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Jobs\AuditExtensionCookbookHealthJob;
use Illuminate\Support\Facades\Log;

it('declares a bounded retry budget with backoff', function (): void {
    $job = new AuditExtensionCookbookHealthJob;

    expect($job->tries)->toBe(3)
        ->and($job->backoff)->toBe(60);
});

it('logs exhausted failures with only the exception class', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(static fn (string $message, array $context): bool => $message === 'Extension Cookbook health audit job exhausted its queue retry window.'
            && $context === [
                'exception_class' => RuntimeException::class,
            ]);

    (new AuditExtensionCookbookHealthJob)->failed(new RuntimeException('dsn=mysql://secret.example.test/database'));
});

it('records exhausted failures safely when no exception is provided', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(static fn (string $message, array $context): bool => $message === 'Extension Cookbook health audit job exhausted its queue retry window.'
            && $context === ['exception_class' => null]);

    (new AuditExtensionCookbookHealthJob)->failed(null);
});
