# Capell Extension Cookbook reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/extension-cookbook`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Capell Extension Cookbook is an **Available**, **Schema-owning** Capell package in the **Capell Essentials** product group. It ships as `capell-app/extension-cookbook` and extends these surfaces: admin, frontend, shared.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/extension-cookbook`
- Namespace: `Capell\ExtensionCookbook`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\ExtensionCookbook\Providers\MigrationMetadataServiceProvider`
- `Capell\ExtensionCookbook\Providers\ExtensionCookbookServiceProvider`
- `Capell\ExtensionCookbook\Providers\AdminServiceProvider`
- `Capell\ExtensionCookbook\Providers\FrontendServiceProvider`

### Config files

- `packages/extension-cookbook/config/extension-cookbook.php`

### Migrations

- `packages/extension-cookbook/database/migrations/2026_08_29_000001_create_extension_cookbook_entries.php`
- `packages/extension-cookbook/database/migrations/2026_08_29_000002_scope_extension_cookbook_entry_slugs.php`

### Settings migrations

- `packages/extension-cookbook/database/settings/2026_08_29_000001_add_extension_cookbook_settings.php`

### Settings classes

- `ExtensionCookbookSettings`

### Models

- `ReferenceEntry`

### Filament classes

- `ExtensionCookbookConfigurator`
- `ExtensionCookbookAdminActionExtender`
- `ExtensionCookbookPageSchemaExtender`
- `ExtensionCookbookPage`
- `ListReferenceEntries`
- `ReferenceEntryResource`
- `ExtensionCookbookSettingsSchema`
- `ExtensionCookbookDashboardWidget`
- `ExtensionCookbookOverviewStat`

### Route files

- `packages/extension-cookbook/routes/web.php`

### Policies

- `ReferenceEntryPolicy`

### Events

- `ReferenceEntryPublished`

### Listeners

- `ReferenceEntrySubscriber`

### Actions

- `AfterInstallExtensionCookbookPackageAction`
- `CreateReferenceEntryAction`
- `EnsureExtensionCookbookExampleAction`
- `InstallExtensionCookbookPackageAction`
- `ReadReferenceEntriesAction`
- `SetupExtensionCookbookPackageAction`
- `UninstallExtensionCookbookPackageAction`

### Data objects

- `ReferenceEntryData`
- `ReferenceEntryPublishedPayloadData`

### Jobs

- `AuditExtensionCookbookHealthJob`

### Command signatures

- `capell:extension-cookbook-doctor`

### Manifest action API

- `afterInstall: Capell\ExtensionCookbook\Actions\AfterInstallExtensionCookbookPackageAction`
- `install: Capell\ExtensionCookbook\Actions\InstallExtensionCookbookPackageAction`
- `setup: Capell\ExtensionCookbook\Actions\SetupExtensionCookbookPackageAction`
- `uninstall: Capell\ExtensionCookbook\Actions\UninstallExtensionCookbookPackageAction`

### Scheduled commands

- `Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookScheduledJobContribution (manifest declared)`

### Console command classes

- `ExtensionCookbookDoctorCommand`

### Manifest contributions

- `admin-action-extender: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookAdminActionExtenderContribution`
- `admin-page: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookAdminPageContribution`
- `admin-resource: Capell\ExtensionCookbook\Manifest\Admin\ReferenceEntryResourceContribution`
- `agent-capability: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookAgentCapabilityContribution`
- `asset: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookAssetContribution`
- `blueprint-subject: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookBlueprintSubjectContribution`
- `configurator: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookConfiguratorContribution`
- `console-command: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookConsoleCommandsContribution`
- `content-graph: Capell\ExtensionCookbook\ContentGraph\ReferenceEntryContentGraphExtractor`
- `content-widget: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookContentWidgetContribution`
- `dashboard-widget: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookDashboardWidgetContribution`
- `dashboard-widget: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookOverviewStatContribution`
- `frontend-component: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookFrontendComponentContribution`
- `health-check: Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck`
- `migration: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookMigrationContribution`
- `model: Capell\ExtensionCookbook\Manifest\Core\ReferenceEntryModelContribution`
- `outbound-event: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookOutboundEventContribution`
- `page-type: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookPageTypeContribution`
- `page-variation: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookPageVariationContribution`
- `permission: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookPermissionsContribution`
- `render-hook: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookRenderHookContribution`
- `route: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookRoutesContribution`
- `scheduled-job: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookScheduledJobContribution`
- `schema-extender: Capell\ExtensionCookbook\Manifest\Admin\ExtensionCookbookSchemaExtenderContribution`
- `section: Capell\ExtensionCookbook\Manifest\Frontend\ExtensionCookbookSectionContribution`
- `setting: Capell\ExtensionCookbook\Manifest\Core\ExtensionCookbookSettingContribution`
- `workflow-attention: Capell\ExtensionCookbook\Admin\Workflow\ExtensionCookbookWorkflowAttentionContribution`

### Health checks

- `Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck`

### Blade views

- `packages/extension-cookbook/resources/views/filament/pages/extension-cookbook.blade.php`
- `packages/extension-cookbook/resources/views/filament/widgets/dashboard.blade.php`
- `packages/extension-cookbook/resources/views/frontend/index.blade.php`
- `packages/extension-cookbook/resources/views/widget/extension-cookbook.blade.php`

### Cache tags

- `extension-cookbook`


## Data Model

- Required tables: `extension_cookbook_entries`.
- Models: `ReferenceEntry`.
- Core record references in migrations: `sites via site_id`.
- Migration files: `2026_08_29_000001_create_extension_cookbook_entries.php`, `2026_08_29_000002_scope_extension_cookbook_entry_slugs.php`.
- Migration impact: run host migrations through the package install flow before opening package surfaces.
- Deletion/retention behaviour: migrations declare cascade-on-delete relationships; no timed pruning or retention schedule is declared in `capell.json`.

<!-- prettier-ignore-end -->
