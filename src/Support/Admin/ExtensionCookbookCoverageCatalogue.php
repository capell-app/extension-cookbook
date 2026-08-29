<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Admin;

use Capell\Core\Enums\ExtensionContributionType;

final class ExtensionCookbookCoverageCatalogue
{
    /**
     * @return array<string, list<array{type: string, status: string, purpose: string}>>
     */
    public static function entries(): array
    {
        $entries = [];

        foreach (self::contributionGroups() as $group => $types) {
            $entries[$group] = array_map(
                static fn (ExtensionContributionType $type): array => [
                    'type' => $type->value,
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.' . self::contributionStatuses()[$type->value]),
                    'purpose' => (string) __('capell-extension-cookbook::admin.coverage.types.' . $type->value),
                ],
                $types,
            );
        }

        return $entries;
    }

    /**
     * Registrar methods are documented separately from the contribution-type
     * catalogue. The inactive entries make the replacement boundary explicit.
     *
     * @return array<string, list<array{method: string, status: string, reason: string}>>
     */
    public static function registrarCoverage(): array
    {
        return [
            'safe' => [
                [
                    'method' => 'page',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.page'),
                ],
                [
                    'method' => 'resource',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.resource'),
                ],
                [
                    'method' => 'extensionDashboardFilamentWidget',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.extension_dashboard_widget'),
                ],
                [
                    'method' => 'filamentDashboardWidget',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.filament_dashboard_widget'),
                ],
                [
                    'method' => 'resourceHeaderActionExtender',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.resource_header_action_extender'),
                ],
                [
                    'method' => 'schemaExtender',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.schema_extender'),
                ],
                [
                    'method' => 'configurator',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.configurator'),
                ],
                [
                    'method' => 'extensionPage',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.demonstrated'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.extension_page'),
                ],
            ],
            'inactive' => [
                [
                    'method' => 'dashboardPage',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.inactive'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.dashboard_page'),
                ],
                [
                    'method' => 'extensionRemovalCoordinator',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.inactive'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.extension_removal_coordinator'),
                ],
                [
                    'method' => 'pendingThemeInstallProvider',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.inactive'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.pending_theme_install_provider'),
                ],
                [
                    'method' => 'settingsSchema',
                    'status' => (string) __('capell-extension-cookbook::admin.coverage.statuses.inactive'),
                    'reason' => (string) __('capell-extension-cookbook::admin.coverage.registrar.methods.settings_schema'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, list<ExtensionContributionType>>
     */
    private static function contributionGroups(): array
    {
        return [
            'Core' => [
                ExtensionContributionType::PageType,
                ExtensionContributionType::Model,
                ExtensionContributionType::Setting,
                ExtensionContributionType::Migration,
                ExtensionContributionType::AgentCapability,
                ExtensionContributionType::ContentGraph,
                ExtensionContributionType::BlueprintSubject,
            ],
            'Admin' => [
                ExtensionContributionType::AdminPage,
                ExtensionContributionType::AdminResource,
                ExtensionContributionType::AdminActionExtender,
                ExtensionContributionType::DashboardFilamentWidget,
                ExtensionContributionType::OverviewStat,
                ExtensionContributionType::SchemaExtender,
                ExtensionContributionType::Configurator,
                ExtensionContributionType::Permission,
                ExtensionContributionType::WorkflowAttention,
            ],
            'Frontend' => [
                ExtensionContributionType::Section,
                ExtensionContributionType::Route,
                ExtensionContributionType::PageVariation,
                ExtensionContributionType::FrontendComponent,
                ExtensionContributionType::ContentWidget,
                ExtensionContributionType::RenderHook,
                ExtensionContributionType::Asset,
                ExtensionContributionType::ScheduledJob,
                ExtensionContributionType::ConsoleCommand,
                ExtensionContributionType::HealthCheck,
                ExtensionContributionType::OutboundEvent,
            ],
        ];
    }

    /**
     * @return array<string, 'demonstrated'|'inactive'>
     */
    private static function contributionStatuses(): array
    {
        return [
            'admin-page' => 'demonstrated',
            'admin-resource' => 'demonstrated',
            'admin-action-extender' => 'demonstrated',
            'section' => 'inactive',
            'page-type' => 'demonstrated',
            'dashboard-widget' => 'demonstrated',
            'overview-stat' => 'demonstrated',
            'schema-extender' => 'demonstrated',
            'configurator' => 'demonstrated',
            'model' => 'demonstrated',
            'permission' => 'demonstrated',
            'route' => 'demonstrated',
            'setting' => 'demonstrated',
            'page-variation' => 'inactive',
            'frontend-component' => 'demonstrated',
            'content-widget' => 'demonstrated',
            'render-hook' => 'demonstrated',
            'asset' => 'demonstrated',
            'migration' => 'demonstrated',
            'scheduled-job' => 'demonstrated',
            'console-command' => 'demonstrated',
            'agent-capability' => 'inactive',
            'content-graph' => 'demonstrated',
            'health-check' => 'demonstrated',
            'workflow-attention' => 'demonstrated',
            'outbound-event' => 'demonstrated',
            'blueprint-subject' => 'demonstrated',
        ];
    }
}
