<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

/**
 * Class FlickrDownload
 * @property int $user_id
 * @property string $photo_id
 * @property string $name
 * @property string $google_album_id
 * @property int $processed
 * @package App\Models\Flickr
 */
class FlickrDownload extends Mongodb
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'photos_count',
        'processed'
    ];

    public function incProcessed()
    {
        $download = FlickrDownload::find($this->_id);
        $download->increment('processed', 1);
        $download->touch();
    }
}
