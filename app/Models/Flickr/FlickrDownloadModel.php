<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;
use Carbon\Carbon;

/**
 * Class FlickrDownload
 * @property int $user_id
 * @property string $photo_id
 * @property string $google_album_id
 * @property string $local_path
 * @property string $google_photo_token
 * @property Carbon $updated_at
 * @package App\Models\Flickr
 */
class FlickrDownloadModel extends Mongodb
{
    protected $fillable = [
        'user_id',
        'photo_id',
        'google_album_id',
        'local_path',
        'google_photo_token',
    ];

    protected $collection = 'flickr_downloads';

    public const GOOGLE_PHOTO_TOKEN = 'google_photo_token';
    public const GOOGLE_ALBUM_ID = 'google_album_id';
    public const PHOTO_ID = 'photo_id';
    public const UPDATED_AT = 'updated_at';
    public const USER_ID = 'user_id';
}
