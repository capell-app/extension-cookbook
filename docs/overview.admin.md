Capell Extension Cookbook is the MIT-licensed canonical runnable demo and reference
repository for the documented public Capell extension contracts. It is intentionally
small: each example has an owning class, a
manifest contribution, and a documented runtime registration. A row marked
"manifest only" is deliberately inactive and is not presented as a working
runtime surface.

Browse the [public source repository](https://github.com/capell-app/extension-cookbook/tree/4.x),
the [manifest](https://github.com/capell-app/extension-cookbook/blob/4.x/capell.json),
and the [package test suite](https://github.com/capell-app/extension-cookbook/tree/4.x/tests)
alongside this catalogue.

## Complete contribution map

The table below covers every current `ExtensionContributionType` value (28 at
the time of writing). Contract validation comes from the Core manifest
validator. Runtime registration is owned by the provider named in the last
column.

| Type                    | Contribution class                                                          | Runtime class or wiring                                                                                                                                           | Status                               |
| ----------------------- | --------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------ |
| `admin-page`            | `Manifest\\Admin\\ExtensionCookbookAdminPageContribution`                   | `AdminServiceProvider` registers `ExtensionCookbookAdminBridge`; bridge calls `AdminBridgeRegistrar::page()`                                                      | Demonstrated                         |
| `admin-resource`        | `Manifest\\Admin\\ReferenceEntryResourceContribution`                       | Bridge calls `AdminBridgeRegistrar::resource()` for `ReferenceEntryResource`                                                                                      | Demonstrated                         |
| `admin-action-extender` | `Manifest\\Admin\\ExtensionCookbookAdminActionExtenderContribution`         | Bridge calls `resourceHeaderActionExtender()`                                                                                                                     | Demonstrated                         |
| `section`               | `Manifest\\Frontend\\ExtensionCookbookSectionContribution`                  | No current section registrar is used by this package                                                                                                              | Manifest only, deliberately inactive |
| `page-type`             | `Manifest\\Core\\ExtensionCookbookPageTypeContribution`                     | `ExtensionCookbookServiceProvider::bootInstalledPackage()` calls `PackageSurfaceRegistrar::blueprintSubject()`, which transitively registers the page type        | Demonstrated (transitive)            |
| `dashboard-widget`      | `Manifest\\Admin\\ExtensionCookbookDashboardWidgetContribution`             | Bridge calls `extensionDashboardFilamentWidget()`                                                                                                                 | Demonstrated                         |
| `dashboard-widget`      | `Manifest\\Admin\\ExtensionCookbookOverviewStatContribution`                | Bridge calls `filamentDashboardWidget(..., DashboardEnum::Main)`                                                                                                  | Demonstrated                         |
| `overview-stat`         | None                                                                        | This package uses a Filament dashboard widget rather than Capell's overview-stat registry                                                                         | Not declared, deliberately inactive  |
| `schema-extender`       | `Manifest\\Admin\\ExtensionCookbookSchemaExtenderContribution`              | Bridge calls `schemaExtender()` for the Page tag                                                                                                                  | Demonstrated                         |
| `configurator`          | `Manifest\\Admin\\ExtensionCookbookConfiguratorContribution`                | Bridge calls `configurator()`                                                                                                                                     | Demonstrated                         |
| `model`                 | `Manifest\\Core\\ReferenceEntryModelContribution`                           | `ExtensionCookbookServiceProvider::bootInstalledPackage()` calls `PackageSurfaceRegistrar::models([ReferenceEntry::class])`                                       | Demonstrated                         |
| `permission`            | `Manifest\\Admin\\ExtensionCookbookPermissionsContribution`                 | `ReferenceEntryResource::canAccess()` checks the package permission gate                                                                                          | Demonstrated                         |
| `route`                 | `Manifest\\Frontend\\ExtensionCookbookRoutesContribution`                   | `FrontendServiceProvider` loads `routes/web.php`                                                                                                                  | Demonstrated                         |
| `setting`               | `Manifest\\Core\\ExtensionCookbookSettingContribution`                      | Core provider calls `settingsClass()` and `settingsMetadata()`; `settingsClass()` transitively derives and registers the schema                                   | Demonstrated (transitive)            |
| `page-variation`        | `Manifest\\Core\\ExtensionCookbookPageVariationContribution`                | No page-variation registrar is used by this package                                                                                                               | Manifest only, deliberately inactive |
| `frontend-component`    | `Manifest\\Frontend\\ExtensionCookbookFrontendComponentContribution`        | Provider tags `ExtensionCookbookFrontendComponentContributor`, which returns a typed Blade component contribution                                                 | Demonstrated                         |
| `public-render-data`    | None                                                                        | No package-owned public render-data contributor is claimed                                                                                                        | Not declared, deliberately inactive  |
| `content-widget`        | `Manifest\\Frontend\\ExtensionCookbookContentWidgetContribution`            | Provider registers `WidgetExtensionDefinitionData` through `WidgetExtensionRegistrar`                                                                             | Demonstrated                         |
| `render-hook`           | `Manifest\\Frontend\\ExtensionCookbookRenderHookContribution`               | Provider calls `FrontendHookRegistrar::contribute()` with a route target and cache-safe declaration                                                               | Demonstrated                         |
| `asset`                 | `Manifest\\Frontend\\ExtensionCookbookAssetContribution`                    | Provider registers the typed CSS and JavaScript group in `FrontendResourceRegistry`                                                                               | Demonstrated                         |
| `migration`             | `Manifest\\Core\\ExtensionCookbookMigrationContribution`                    | Core provider declares the package migration in `configurePackage()`                                                                                              | Demonstrated                         |
| `scheduled-job`         | `Manifest\\Frontend\\ExtensionCookbookScheduledJobContribution`             | `ExtensionCookbookServiceProvider` schedules `AuditExtensionCookbookHealthJob` daily in console context                                                           | Demonstrated                         |
| `console-command`       | `Manifest\\Frontend\\ExtensionCookbookConsoleCommandsContribution`          | `ExtensionCookbookServiceProvider` registers `ExtensionCookbookDoctorCommand` in console context                                                                  | Demonstrated                         |
| `agent-capability`      | `Manifest\\Core\\ExtensionCookbookAgentCapabilityContribution`              | No agent capability runtime is claimed by this package                                                                                                            | Manifest only, deliberately inactive |
| `content-graph`         | `ContentGraph\\ReferenceEntryContentGraphExtractor`                         | `ExtensionCookbookServiceProvider` registers the extractor with `ContentGraphRegistry`; persisted entry and Page site IDs must match before a relation is emitted | Demonstrated                         |
| `health-check`          | `Manifest\\Frontend\\ExtensionCookbookHealthContribution`                   | `ExtensionCookbookDoctorCommand` invokes `ExtensionCookbookHealthCheck::runDiagnostics()`                                                                         | Demonstrated                         |
| `workflow-attention`    | `Manifest\\Admin\\Workflow\\ExtensionCookbookWorkflowAttentionContribution` | Conditional contribution returns an item only when its supported actor/context is present                                                                         | Demonstrated                         |
| `outbound-event`        | `Manifest\\Core\\ExtensionCookbookOutboundEventContribution`                | Core provider calls `PackageSurfaceRegistrar::outboundEvent()`                                                                                                    | Demonstrated                         |
| `blueprint-subject`     | `Manifest\\Core\\ExtensionCookbookBlueprintSubjectContribution`             | Core provider calls `PackageSurfaceRegistrar::blueprintSubject()`                                                                                                 | Demonstrated                         |

The package's public widget is `Widget\\ExtensionCookbookWidget`, whose key is
`capell-app.extension-cookbook`. Its input and render boundaries are the
typed `ExtensionCookbookWidgetInputData` and `ExtensionCookbookWidgetRenderData` classes. Public
views receive those hydrated values only.

## Registrar method coverage

The following is an explicit inventory against the current Core contracts.
"Demonstrated" means the package calls the method in its supported provider
boundary. "Demonstrated (transitive)" means the package reaches the contract
through another supported method. "Deliberately inactive" means this reference package does not claim
the method as a shipped example.

### [`PackageSurfaceRegistrar`](https://github.com/capell-app/core/blob/main/src/Support/Packages/PackageSurfaceRegistrar.php#L40)

| Method                                                                                                                            | Status                                             |
| --------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------- |
| `duringPackageInstallation(callable $callback)`                                                                                   | Deliberately inactive                              |
| `pageType(PageTypeData $type)`                                                                                                    | Demonstrated (transitive via `blueprintSubject()`) |
| `outboundEvent(OutboundEventDefinitionData $definition)`                                                                          | Demonstrated                                       |
| `blueprintSubject(BlueprintSubjectDescriptorData $subject)`                                                                       | Demonstrated                                       |
| `component(string \| BackedEnum $type, string \| BackedEnum $name, string $component)`                                            | Deliberately inactive                              |
| `components(string \| BackedEnum $type, array $components)`                                                                       | Deliberately inactive                              |
| `models(array $models)`                                                                                                           | Demonstrated                                       |
| `modelInterceptor(string $model, string $interceptorClass, null \| array \| string \| BackedEnum $key = null, int $priority = 0)` | Demonstrated                                       |
| `subscriber(string $subscriber)`                                                                                                  | Demonstrated                                       |
| `subscriberManager()`                                                                                                             | Deliberately inactive                              |
| `settingsSchema(string $group, string $schemaClass, ?string $key = null)`                                                         | Demonstrated (transitive via `settingsClass()`)    |
| `settingsClass(string $group, string $settingsClass)`                                                                             | Demonstrated                                       |
| `settingsMetadata(SettingsGroupMetadata $metadata)`                                                                               | Demonstrated                                       |
| `metricCollector(string $collectorClass)`                                                                                         | Demonstrated                                       |

### [`AdminBridgeRegistrar`](https://github.com/capell-app/admin/blob/main/src/Support/Bridges/AdminBridgeRegistrar.php#L50)

| Method                                                                                                 | Status                |
| ------------------------------------------------------------------------------------------------------ | --------------------- |
| `bridge(string $packageName, string $bridgeClass)`                                                     | Deliberately inactive |
| `page(string $pageClass)`                                                                              | Demonstrated          |
| `report(ReportDefinitionData $report)`                                                                 | Deliberately inactive |
| `dashboardPage(string $pageClass)`                                                                     | Deliberately inactive |
| `resource(string $resourceClass, string $group, string $name = 'default')`                             | Demonstrated          |
| `widget(string $widgetClass)`                                                                          | Deliberately inactive |
| `filamentDashboardWidget(string $widgetClass, DashboardEnum ...$dashboards)`                           | Demonstrated          |
| `dashboardPanel(DashboardRegionEnum $region, string $widgetClass, DashboardEnum ...$dashboards)`       | Deliberately inactive |
| `extensionDashboardFilamentWidget(string $widgetClass)`                                                | Demonstrated          |
| `extensionHealthProvider(string $providerClass)`                                                       | Deliberately inactive |
| `extensionRuntimeCheckProvider(string $providerClass)`                                                 | Deliberately inactive |
| `extensionQuickActionProvider(string $providerClass)`                                                  | Deliberately inactive |
| `extensionUpdateMetadataProvider(string $providerClass)`                                               | Deliberately inactive |
| `extensionDependencyProvider(string $providerClass)`                                                   | Deliberately inactive |
| `extensionsPageExtender(string $extenderClass)`                                                        | Deliberately inactive |
| `extensionCatalogueMetadataProvider(string $providerClass)`                                            | Deliberately inactive |
| `resourceHeaderActionExtender(string $extenderClass)`                                                  | Demonstrated          |
| `extensionRemovalCoordinator(string $coordinatorClass)`                                                | Deliberately inactive |
| `pendingThemeInstallProvider(string $providerClass)`                                                   | Deliberately inactive |
| `extensionsPageHeaderAction(Action \| ActionGroup \| Closure $action, ?string $key = null)`            | Deliberately inactive |
| `extensionsPageHeaderActionGroupAction(Action \| ActionGroup \| Closure $action, ?string $key = null)` | Deliberately inactive |
| `extensionsPageTableAction(Action \| Closure $action, ?string $key = null)`                            | Deliberately inactive |
| `userMenuItem(...)`                                                                                    | Deliberately inactive |
| `workspace(AdminWorkspaceItemData $item)`                                                              | Deliberately inactive |
| `welcomeTourStep(...)`                                                                                 | Deliberately inactive |
| `configurator(string $configuratorClass, string $group, string $name)`                                 | Demonstrated          |
| `schemaExtender(string $extenderClass, string $tag)`                                                   | Demonstrated          |
| `panelExtender(string $extenderClass)`                                                                 | Deliberately inactive |
| `userResourceBridge(string $bridgeClass, bool $scoped = true)`                                         | Deliberately inactive |
| `dashboardSettingsContributor(string $contributorClass)`                                               | Deliberately inactive |
| `extensionPage(string $packageName, string $pageClass)`                                                | Demonstrated          |
| `extensionManagementSurface(ExtensionManagementSurfaceData $surface)`                                  | Deliberately inactive |
| `activityChangeSetBuilder(string $builderClass)`                                                       | Deliberately inactive |
| `activityRevertHandler(string $handlerClass)`                                                          | Deliberately inactive |
| `activityResourceLink(...)`                                                                            | Deliberately inactive |
| `settingsSchema(string $group, string $schemaClass, ?string $key = null)`                              | Deliberately inactive |
| `settingsClass(string $group, string $settingsClass)`                                                  | Deliberately inactive |
| `settingsMetadata(SettingsGroupMetadata $metadata)`                                                    | Deliberately inactive |

`AdminBridgeRegistrar::settingsSchema()` is deliberately inactive for this
package. The Core provider is the canonical owner of the setting class, schema,
and metadata through `PackageSurfaceRegistrar`; the Admin bridge registers only
admin pages, resources, widgets, and extenders.

### [`FrontendHookRegistrar`](https://github.com/capell-app/frontend/blob/main/src/Support/Render/FrontendHookRegistrar.php#L22)

| Method                                                                                                                                                                                                                  | Status       |
| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------ |
| `contribute(RenderHookLocation $location, RenderHookExtensionInterface \| string $extension, string $owner, string $key, int $priority = 10, ?string $scenario = null, ?string $target = null, bool $cacheSafe = true)` | Demonstrated |

## Safety boundaries and programme gaps

All writes remain in Actions. `ReadReferenceEntriesAction` hydrates
`ReferenceEntryData` before the controller renders the page. Public Blade only
escapes and prints those values. It does not query, resolve the container,
lazy-load a relation, print IDs, expose field paths, emit authoring markers,
or include signed editor state. The route and widget fallback remain useful
when JavaScript is disabled. The render hook is target-scoped and declares
`cacheSafe: true`.

Reference entries persist a required `site_id`. The create Action rejects a
related Page from another site, and the graph extractor repeats that check
against persisted Page state before emitting its weak relation. This prevents
cross-site Page links rather than treating the package's records as global.

Provider bucket declarations express the intended metadata, install, runtime,
admin, and frontend boundaries, but this package does not claim request-context
isolation proof for those buckets. That remains a CAP-0467/CAP-0470 follow-up.

This package is a positive consumer of the released contracts that exist today.
The current Core `PublicRenderDataContributor` seam is marked experimental and
is not in the released dependency contract consumed by this package, so it
remains an explicit programme gap rather than a manifest claim. See the [Core
contract](https://github.com/capell-app/capell/blob/main/packages/frontend/src/Contracts/PublicRenderDataContributor.php#L20-L36)
and [registry](https://github.com/capell-app/capell/blob/main/packages/frontend/src/Support/Render/PublicRenderDataContributorRegistry.php#L19-L64)
when evaluating that follow-up. The package also does not claim runtime
receipts or receipt reconciliation (CAP-0467), relative ordering and collision
policy (CAP-0468), or stable Admin zones (CAP-0471).

## Marketplace and screenshots

The extension is MIT-licensed and first-party. Authentic route and installed-host
captures are described in `docs/screenshots.json`; no screenshot in this
package is presented as release evidence until produced by the shared runner.

## Troubleshooting

If an example is not visible, check that the package is installed and enabled
for the current site and that setup ran with a valid site context. Manifest-only
rows are intentionally inactive. Use the focused package test named by the
relevant table row before changing registration code.
