<?php


namespace Tests\Feature\Command;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Command\Traits\CommandSucceed;
use Tests\TestCase;

class FlickrTest extends TestCase
{
    use RefreshDatabase;
    use CommandSucceed;

    protected $commands = [
        'flickr:contacts',
        'flickr:photos',
        'flickr:photossizes',
    ];
}
