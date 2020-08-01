<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;
use App\Facades\FlickrClient;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class FlickrPhotoModel
 * @package App\Models\Flickr
 */
class FlickrPhoto extends Mongodb implements FlickrPhotoInterface
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
        return $this->belongsTo(FlickrContact::class, self::KEY_OWNER, FlickrContact::KEY_NSID);
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

    public function getSizes(): array
    {
        if ($this->hasSizes()) {
            return $this->{self::KEY_SIZES};
        }

        // @TODO Exception can't get sizes
        $this->{self::KEY_SIZES} = FlickrClient::getPhotoSizes($this->id)->sizes->size;
        $this->save();

        return $this->{self::KEY_SIZES};
    }

    public function getBestSize(): ?array
    {
        $sizes = $this->getSizes();

        if (!$sizes) {
            return null;
        }

        $sizes = end($sizes);

        if (is_object($sizes)) {
            return get_object_vars($sizes);
        }

        return $sizes;
    }

    /**
     * @return bool
     */
    public function hasSizes(): bool
    {
        return !empty($this->{self::KEY_SIZES});
    }
}
