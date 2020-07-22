<?php

namespace App\Jobs;

class Queues
{
    const QUEUE_JAV           = 'jav';
    const QUEUE_TRUYENTRANH   = 'truyentranh';
    const QUEUE_BATDONGSAN    = 'batdongsan';
    const QUEUE_DOWNLOADS     = 'downloads';
    const QUEUE_FLICKR        = 'flickr';
    const QUEUE_GOOGLE        = 'google';

    const QUEUES = [
        self::QUEUE_JAV,
        self::QUEUE_TRUYENTRANH,
        self::QUEUE_DOWNLOADS,
        self::QUEUE_BATDONGSAN,
        self::QUEUE_FLICKR,
        self::QUEUE_GOOGLE,
    ];
}
