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
    public const KEY_PAGE = 'page';
    public const KEY_KEYWORD = 'keyword';

    /* JAV Idols */
    public const JAV_IDOLS_FILTER_AGE_FROM = 'filter_age_from';
    public const JAV_IDOLS_FILTER_AGE_TO = 'filter_age_to';
    public const JAV_IDOLS_FILTER_HEIGHT = 'filter_height';
    public const JAV_IDOLS_FILTER_BREAST = 'filter_breast';
    public const JAV_IDOLS_FILTER_WAIST = 'filter_waist';
    public const JAV_IDOLS_FILTER_HIPS = 'filter_hips';
    public const JAV_IDOLS_FILTER_CITY = 'filter_city';

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
    public const KEY_JAV_MOVIES_FILTER_IDOL_HEIGHT = 'filter_idol_height';
    public const KEY_JAV_MOVIES_FILTER_IDOL_BREAST = 'filter_idol_breast';
    public const KEY_JAV_MOVIES_FILTER_IDOL_WAIST = 'filter_idol_waist';
    public const KEY_JAV_MOVIES_FILTER_IDOL_HIPS = 'filter_idol_hips';
    public const KEY_JAV_MOVIES_FILTER_IDOL_AGE_FROM = 'filter_idol_age_from';
    public const KEY_JAV_MOVIES_FILTER_IDOL_AGE_TO = 'filter_idol_age_to';
}
