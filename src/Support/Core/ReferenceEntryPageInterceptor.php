<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Core;

use Capell\Core\Contracts\ModelInterceptors\PageInterceptorInterface;
use Capell\Core\Contracts\Pageable;

final class ReferenceEntryPageInterceptor implements PageInterceptorInterface
{
    public const string KEY = 'extension-cookbook.entry';

    public function beforeCreate(array $data): array
    {
        return $data;
    }

    public function afterCreated(Pageable $page, array $data): void {}
}
