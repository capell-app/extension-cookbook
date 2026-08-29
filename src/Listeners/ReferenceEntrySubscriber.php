<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Listeners;

use Capell\Core\Support\Subscriber\Contracts\Subscriber;
use Capell\ExtensionCookbook\Events\ReferenceEntryPublished;

final class ReferenceEntrySubscriber implements Subscriber
{
    public function handle(string $event, object $context): void
    {
        if ($event !== ReferenceEntryPublished::class || ! $context instanceof ReferenceEntryPublished) {
            return;
        }
    }
}
