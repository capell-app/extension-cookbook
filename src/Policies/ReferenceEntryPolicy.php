<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Policies;

use Capell\Admin\Support\SiteScope;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

final class ReferenceEntryPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $this->allows($user, 'View:ExtensionCookbook');
    }

    public function view(?Authenticatable $user, ReferenceEntry $entry): bool
    {
        return $this->viewAny($user) && $this->canUseEntrySite($user, $entry);
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->allows($user, 'Manage:ExtensionCookbook');
    }

    public function update(?Authenticatable $user, ReferenceEntry $entry): bool
    {
        return $this->create($user) && $this->canUseEntrySite($user, $entry);
    }

    public function delete(?Authenticatable $user, ReferenceEntry $entry): bool
    {
        return $this->create($user) && $this->canUseEntrySite($user, $entry);
    }

    private function allows(?Authenticatable $user, string $permission): bool
    {
        return $user !== null && Gate::forUser($user)->allows($permission);
    }

    private function canUseEntrySite(?Authenticatable $user, ReferenceEntry $entry): bool
    {
        if (! $user instanceof Authenticatable) {
            return false;
        }

        if (SiteScope::isGlobalActor($user)) {
            return true;
        }

        $siteId = $entry->getAttribute('site_id');

        return is_numeric($siteId) && $user->getAssignedSiteIds()->contains((int) $siteId);
    }
}
