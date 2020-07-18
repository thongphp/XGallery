<?php

namespace App\Repositories;

final class ConfigRepository
{
    /* Default value */
    public const DEFAULT_PER_PAGE = 15;

    /* Keys */
    public const KEY_SORT_BY = 'sortBy';
    public const KEY_SORT_DIRECTION = 'sortDir';
    public const KEY_PER_PAGE = 'perPage';
}
