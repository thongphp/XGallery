<?php

namespace App\Models\Flickr;

use App\Database\Mongodb;

class Photo extends Mongodb implements PhotoInterface
{
    public const KEY_OWNER = 'owner';
    public const KEY_STATUS = 'status';

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
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function refOwner()
    {
        return $this->belongsTo(Contact::class, self::KEY_OWNER, Contact::KEY_NSID);
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        return !$this->sizes ? '' : $this->sizes[0]['source'];
    }

    /**
     * @return bool
     */
    public function hasSizes(): bool
    {
        return !empty($this->sizes);
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return (bool) $this->status;
    }
}
