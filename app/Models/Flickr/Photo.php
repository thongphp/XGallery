<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

class Photo extends Mongodb implements PhotoInterface
{
    public const KEY_OWNER = 'owner';

    protected $collection = 'flickr_photos';
    protected $casts = [
        'sizes' => 'json',
    ];
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
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
