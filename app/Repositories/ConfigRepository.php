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

    /* JAV Movies */
    public const KEY_JAV_MOVIES_FILTER_DIRECTOR = 'filter_director';
    public const KEY_JAV_MOVIES_FILTER_STUDIO = 'filter_studios';
    public const KEY_JAV_MOVIES_FILTER_SERIES = 'filter_series';
    public const KEY_JAV_MOVIES_FILTER_CHANNEL = 'filter_channel';
    public const KEY_JAV_MOVIES_FILTER_IDOL = 'filter_idol';
    public const KEY_JAV_MOVIES_FILTER_GENRE = 'filter_genre';
    public const KEY_JAV_MOVIES_FILTER_FROM = 'filter_from';
    public const KEY_JAV_MOVIES_FILTER_TO = 'filter_to';
    public const KEY_JAV_MOVIES_FILTER_DOWNLOADABLE = 'filter_downloadable';
}
