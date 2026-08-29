# Capell Extension Cookbook

Capell Extension Cookbook is the MIT-licensed canonical runnable demo and reference repository for the documented public Capell extension contracts. It is intentionally small: install it when you need a concrete example of typed package data, lifecycle actions, Core registrations, admin bridges, frontend hooks, settings, metrics, health checks, and the public `/extension-cookbook` route. It is not an end-user product or a CAP-0470 conformance harness.

## Install

```bash
composer require capell-app/extension-cookbook
```

Enable the package through the host package manager. Installation and setup are idempotent; uninstall is non-destructive and retains the package-owned `extension_cookbook_entries` table for safe re-enablement.

## Contract map

- Core registrations: `src/Providers/ExtensionCookbookServiceProvider.php`
- Admin bridge: `src/Bridges/ExtensionCookbookAdminBridge.php`
- Frontend integrations: `src/Providers/FrontendServiceProvider.php`
- Lifecycle actions: `src/Actions/*ExtensionCookbookPackageAction.php`
- Manifest contract catalogue: `capell.json` and `docs/extension-points.md`
- Focused tests: `tests/`

The package is a reference implementation, not a general-purpose content model or a substitute for a domain package. Its public output contains no editor controls, package internals, or sensitive data.

## Documentation

- [Overview](docs/overview.md)
- [Extension points](docs/extension-points.md)
- [Screenshot contract](docs/screenshots.json)
