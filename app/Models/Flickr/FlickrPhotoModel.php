<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class FlickrPhotoModel
 * @package App\Models\Flickr
 */
class FlickrPhotoModel extends Mongodb implements FlickrPhotoInterface
{
    use SoftDeletes;

    public const KEY_OWNER = 'owner';
    public const KEY_SIZES = 'sizes';
    public const KEY_ID = 'id';

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
     * Getting owner (Contact) of this photo
     * Example:
     * $this->flickrcontact // Get FlickrModelContact object
     * $this->flickrContact() // Get query builder
     *
     * @return BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function flickrContact()
    {
        return $this->belongsTo(FlickrContactModel::class, self::KEY_OWNER, FlickrContactModel::KEY_NSID);
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        if (empty($this->{self::KEY_SIZES})) {
            return 'https://via.placeholder.com/150';
        }

        return $this->{self::KEY_SIZES}[0]['source'];
    }

    /**
     * @return bool
     */
    public function hasSizes(): bool
    {
        return !empty($this->{self::KEY_SIZES});
    }
}
