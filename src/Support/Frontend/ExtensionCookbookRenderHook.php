<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Frontend;

use Capell\Frontend\Contracts\RenderHookExtensionInterface;
use Capell\Frontend\Data\RenderHookContext;

final class ExtensionCookbookRenderHook implements RenderHookExtensionInterface
{
    public function render(RenderHookContext $context): string
    {
        return '<p>' . e(__('capell-extension-cookbook::frontend.route_note')) . '</p>';
    }
}
