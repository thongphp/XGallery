<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class Photo
 * @package App\Models\Flickr
 * @todo Rename to FlickrPhoto. Laravel will handle collection name
 */
class Photo extends Mongodb implements PhotoInterface
{
    use SoftDeletes;

    public const KEY_OWNER = 'owner';

    protected $collection = 'flickr_photos';
    protected $fillable = [
        'id',
        'owner',
        'secret',
        'server',
        'farm',
        'title',
        'ispublic',
        'isfriend',
        'isfamily',
        'sizes'
    ];

    /**
     * @return BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     * @todo Not sure but use owner() won't work
     */
    public function refOwner()
    {
        return $this->belongsTo(Contact::class, self::KEY_OWNER, Contact::KEY_NSID);
    }

    /**
     * @return null|string
     */
    public function getCover(): ?string
    {
        return $this->sizes ? $this->sizes[0]['source'] : null;
    }

    /**
     * @return bool
     */
    public function hasSizes(): bool
    {
        return !empty($this->sizes);
    }
}
