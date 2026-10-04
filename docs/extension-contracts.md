# Extension and action examples

<!-- Maintained by scripts/generate-package-readmes.php -->

Use the action functions with records and Data objects supplied by your application.
They pass each argument to the package operation and return its result.

These adapters show container registration. Use the owning package registry when
a contract requires contributor discovery.

Contract adapters wrap an existing implementation. Call their registration function
from your service provider with that implementation; tagged contracts keep their declared tag.
Resolve the backend by its concrete class before registration so the replacement contract
does not resolve itself. Static contract metadata uses one backend class per adapter.

## Action `afterInstall`

<!-- example: action afterInstall -->

```php
<?php
declare(strict_types=1);

namespace App\CapellExamples\ExtensionCookbook;

function runAfterInstall(\Capell\Core\Data\PackageData $package, array $arguments = [], ?\Capell\Core\Contracts\ProgressReporter $reporter = null): void
{
    \Capell\ExtensionCookbook\Actions\AfterInstallExtensionCookbookPackageAction::run($package, $arguments, $reporter);
}
```

## Action `install`

<!-- example: action install -->

```php
<?php
declare(strict_types=1);

namespace App\CapellExamples\ExtensionCookbook;

function runInstall(\Capell\Core\Data\PackageData $package, array $arguments = [], ?\Capell\Core\Contracts\ProgressReporter $reporter = null): void
{
    \Capell\ExtensionCookbook\Actions\InstallExtensionCookbookPackageAction::run($package, $arguments, $reporter);
}
```

## Action `setup`

<!-- example: action setup -->

```php
<?php
declare(strict_types=1);

namespace App\CapellExamples\ExtensionCookbook;

function runSetup(\Capell\Core\Data\PackageData $package, array $arguments = [], ?\Capell\Core\Contracts\ProgressReporter $reporter = null): void
{
    \Capell\ExtensionCookbook\Actions\SetupExtensionCookbookPackageAction::run($package, $arguments, $reporter);
}
```

## Action `uninstall`

<!-- example: action uninstall -->

```php
<?php
declare(strict_types=1);

namespace App\CapellExamples\ExtensionCookbook;

function runUninstall(\Capell\Core\Data\PackageData $package, array $arguments = [], ?\Capell\Core\Contracts\ProgressReporter $reporter = null): void
{
    \Capell\ExtensionCookbook\Actions\UninstallExtensionCookbookPackageAction::run($package, $arguments, $reporter);
}
```
