<?php

namespace App\Events;

class FlickrDownloadRequest extends UserActivity
{
    protected string $action = 'download';
    protected ?string $objectId = null;

    /**
     * Create a new event instance.
     * @param  string  $action
     * @param $object
     */
    public function __construct(string $action, $object)
    {
        parent::__construct($action, $object);

        $this->objectId = $this->object->getId();
        // @todo Should be dynamic
        $this->objectTable = 'flickr_album';
    }
}
