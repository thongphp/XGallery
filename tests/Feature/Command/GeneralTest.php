<?php


namespace Tests\Feature\Command;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Command\Traits\CommandSucceed;
use Tests\TestCase;

class GeneralTest extends TestCase
{
    use RefreshDatabase;
    use CommandSucceed;

    protected $commands = [
        'batdongsan',
        'kissgoddess',
        'nhaccuatui',
        'phodacbiet',
        'xiuren',
        'flickr:contacts',
        'flickr:photos',
        'flickr:photossizes',
    ];
}
