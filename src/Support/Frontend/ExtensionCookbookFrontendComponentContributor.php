<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Frontend;

use Capell\Frontend\Contracts\FrontendComponentContributor;
use Capell\Frontend\Data\FrontendComponentContributionData;
use Capell\Frontend\Enums\FrontendComponentTarget;

final class ExtensionCookbookFrontendComponentContributor implements FrontendComponentContributor
{
    /** @return list<FrontendComponentContributionData> */
    public function components(): array
    {
        return [new FrontendComponentContributionData(
            name: 'extension-cookbook.widget',
            component: 'capell-extension-cookbook::widget.extension-cookbook',
            target: FrontendComponentTarget::Blade,
        )];
    }
}
