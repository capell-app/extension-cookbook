<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Admin\Workflow;

use Capell\Admin\Support\SiteScope;
use Capell\Core\Contracts\Extensions\ContributesWorkflowAttention;
use Capell\Core\Data\Workflow\WorkflowAttentionItemData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

final class ExtensionCookbookWorkflowAttentionContribution implements ContributesWorkflowAttention
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    /** @return list<WorkflowAttentionItemData> */
    public function attentionItems(?Authenticatable $user = null): array
    {
        if ($user === null || ! Gate::forUser($user)->allows('View:ExtensionCookbook')) {
            return [];
        }

        if (! Schema::hasTable('extension_cookbook_entries')) {
            return [];
        }

        $entries = ReferenceEntry::query()->where('enabled', true);
        $hasEnabledEntry = SiteScope::isGlobalActor($user)
            ? $entries->exists()
            : $entries->whereIn('site_id', $user->getAssignedSiteIds())->exists();

        if ($hasEnabledEntry) {
            return [];
        }

        return [
            new WorkflowAttentionItemData(
                'capell-app/extension-cookbook',
                __('capell-extension-cookbook::admin.workflow.label'),
                'warning',
                __('capell-extension-cookbook::admin.workflow.owner'),
                __('capell-extension-cookbook::admin.workflow.action'),
                'filament.admin.pages.extension-cookbook',
                permission: 'View:ExtensionCookbook',
            ),
        ];
    }
}
