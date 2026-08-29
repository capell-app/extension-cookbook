<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Metrics;

use Capell\Core\Contracts\Metrics\CollectsDailyMetrics;
use Capell\Core\Data\Metrics\MetricCollectionResultData;
use Capell\Core\Data\Metrics\MetricDefinitionData;
use Capell\Core\Data\Metrics\MetricGovernanceData;
use Capell\Core\Data\Metrics\MetricIdentityData;
use Capell\Core\Data\Metrics\MetricRepresentationData;
use Capell\Core\Data\Metrics\MetricSampleData;
use Capell\Core\Data\Metrics\MetricScopeData;
use Capell\Core\Data\Metrics\MetricSemanticsData;
use Capell\Core\Data\Metrics\MetricValueData;
use Capell\Core\Enums\Metrics\MetricAggregation;
use Capell\Core\Enums\Metrics\MetricBackfillPolicy;
use Capell\Core\Enums\Metrics\MetricCollectionStatus;
use Capell\Core\Enums\Metrics\MetricGapPolicy;
use Capell\Core\Enums\Metrics\MetricScopeType;
use Capell\Core\Enums\Metrics\MetricSemantic;
use Capell\Core\Enums\Metrics\MetricSensitivity;
use Capell\Core\Enums\Metrics\MetricSource;
use Capell\Core\Enums\Metrics\MetricValueType;
use Capell\Core\Enums\Metrics\MetricVisibility;
use Capell\Core\Enums\MetricUnitEnum;
use Capell\ExtensionCookbook\Models\ReferenceEntry;

final class ReferenceEntryMetricsCollector implements CollectsDailyMetrics
{
    public function definitions(): array
    {
        return [$this->definition()];
    }

    public function collect(string $day, array $scopes): MetricCollectionResultData
    {
        $global = array_values(array_filter($scopes, static fn (MetricScopeData $scope): bool => $scope->type === MetricScopeType::Global));
        if ($global === [] || count($global) !== count($scopes)) {
            return new MetricCollectionResultData(MetricCollectionStatus::Unsupported, $day, [], [], null, null, 'Global scopes only.');
        }
        $definition = $this->definition();
        $samples = array_map(fn (MetricScopeData $scope): MetricSampleData => new MetricSampleData($definition->identity, $definition->semanticHash(), $day, $scope, $definition->representation, MetricValueData::integer(ReferenceEntry::query()->enabled()->count())), $global);

        return new MetricCollectionResultData(MetricCollectionStatus::Complete, $day, $global, $samples, 'extension-cookbook:' . $day, null, null);
    }

    private function definition(): MetricDefinitionData
    {
        return new MetricDefinitionData(new MetricIdentityData('capell-app/extension-cookbook', 'showcase', 'entries_enabled'), new MetricRepresentationData(MetricUnitEnum::Count, MetricValueType::Integer), MetricScopeType::Global, new MetricSemanticsData(MetricSemantic::Gauge, MetricAggregation::Last, MetricGapPolicy::Missing, MetricBackfillPolicy::CurrentDayOnly), new MetricGovernanceData(MetricSource::Database, 'extension_cookbook_entries', MetricSensitivity::Internal, MetricVisibility::SiteAdmin), labels: ['en' => 'Enabled showcase entries'], descriptions: ['en' => 'Number of enabled Capell Extension Cookbook entries.']);
    }
}
