<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Actions;

use Capell\Core\Contracts\PackageLifecycleAction;
use Capell\Core\Contracts\ProgressReporter;
use Capell\Core\Data\PackageData;
use Capell\Core\Support\Install\NullProgressReporter;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

final class SetupExtensionCookbookPackageAction implements PackageLifecycleAction
{
    use AsFake;
    use AsObject;

    public function handle(PackageData $package, array $arguments = [], ?ProgressReporter $reporter = null): void
    {
        $reporter ??= new NullProgressReporter;
        EnsureExtensionCookbookExampleAction::run($arguments);
        $reporter->report('Capell Extension Cookbook setup completed successfully.');
    }
}
