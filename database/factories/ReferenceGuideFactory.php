<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Database\Factories;

use Capell\Core\Models\Site;
use Capell\ExtensionCookbook\Models\ReferenceGuide;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<ReferenceGuide>
 */
final class ReferenceGuideFactory extends Factory
{
    protected $model = ReferenceGuide::class;

    /** @return array<string, mixed> */
    #[Override]
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'name' => $this->faker->unique()->words(3, true),
            'visible_from' => now()->subDay(),
        ];
    }
}
