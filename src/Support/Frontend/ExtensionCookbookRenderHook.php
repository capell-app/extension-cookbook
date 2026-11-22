<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Frontend;

use Capell\Core\Contracts\Rendering\RenderHookExtensionInterface;
use Capell\Core\Data\Rendering\RenderHookContext;

final class ExtensionCookbookRenderHook implements RenderHookExtensionInterface
{
    public function render(RenderHookContext $context): string
    {
        return '<p>' . e(__('capell-extension-cookbook::frontend.route_note')) . '</p>';
    }
}
