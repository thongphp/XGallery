<?php

namespace App\Jobs;

class Queues
{
    const QUEUE_DEFAULT       = 'default'; // 6
    const QUEUE_JAV           = 'jav'; // 6
    const QUEUE_TRUYENTRANH   = 'truyentranh'; // 8
    const QUEUE_DOWNLOADS     = 'downloads';
    const QUEUE_BATDONGSAN    = 'batdongsan'; // 6
    const QUEUE_PHOTOS        = 'photos';
    const QUEUE_FLICKR        = 'flickr'; // 4
    const QUEUE_GOOGLE        = 'google'; // 4

    const QUEUES = [
        self::QUEUE_DEFAULT,
        self::QUEUE_JAV,
        self::QUEUE_TRUYENTRANH,
        self::QUEUE_DOWNLOADS,
        self::QUEUE_BATDONGSAN,
        self::QUEUE_PHOTOS,
        self::QUEUE_FLICKR,
        self::QUEUE_GOOGLE,
    ];
}
