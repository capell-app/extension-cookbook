<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Settings;

use Capell\Core\Contracts\SettingsContract;
use Capell\Core\Contracts\SettingsSchema;
use Capell\Core\Contracts\SettingsSchemaContract;
use Capell\ExtensionCookbook\Filament\Settings\ExtensionCookbookSettingsSchema;
use Spatie\LaravelSettings\Settings;

final class ExtensionCookbookSettings extends Settings implements SettingsContract, SettingsSchemaContract
{
    public bool $enabled = true;

    public static function group(): string
    {
        return 'extension_cookbook';
    }

    /** @return class-string<SettingsSchema> */
    public static function schema(): string
    {
        return ExtensionCookbookSettingsSchema::class;
    }
}
