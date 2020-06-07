<?php

namespace Tests\Unit\Services\Flickr;

use App\Facades\Flickr;
use App\Services\Flickr\Url\FlickrUrl;
use App\Services\Flickr\UrlExtractor;
use Tests\TestCase;

class UrlExtractorTest extends TestCase
{
    /**
     * @return array|string[][]
     */
    public function extractFromUrlDataProvider(): array
    {
        return [
            [
                'url' => 'https://www.flickr.com/photos/flickr/albums/72157707851154934',
                'expectType' => 'album',
                'expectId' => '72157707851154934',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/photos/236127362@N93/albums/72157707851154934',
                'expectType' => 'album',
                'expectId' => '72157707851154934',
                'expectOwner' => '236127362@N93',
            ],
            [
                'url' => 'https://www.flickr.com/photos/92537543@N08/albums/72157684206305213',
                'expectType' => 'album',
                'expectId' => '72157707851154934',
                'expectOwner' => 'flickr',
            ],
            [
                'url' => 'https://www.flickr.com/photos/baraods/albums/93826484219372866?foo=bar',
                'expectType' => 'album',
                'expectId' => '93826484219372866',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/photos/flickr/galleries/72157714491688536',
                'expectType' => 'gallery',
                'expectId' => '72157714491688536',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/photos/236127362@N93/galleries/72157714491688536',
                'expectType' => 'gallery',
                'expectId' => '72157714491688536',
                'expectOwner' => '236127362@N93',
            ],
            [
                'url' => 'https://www.flickr.com/photos/kingnik/49956954471',
                'expectType' => 'photo',
                'expectId' => '49956954471',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/photos/236127362@N93/49956954471',
                'expectType' => 'photo',
                'expectId' => '49956954471',
                'expectOwner' => '236127362@N93',
            ],
            [
                'url' => 'https://www.flickr.com/photos/kingnik/123281738273/in/fave-1237123627@N3/',
                'expectType' => 'photo',
                'expectId' => '123281738273',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/people/flickr/',
                'expectType' => 'profile',
                'expectId' => 'flickr',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/people/236127362@N93/',
                'expectType' => 'profile',
                'expectId' => '236127362@N93',
                'expectOwner' => '236127362@N93',
            ],
        ];
    }

    /**
     * @dataProvider extractFromUrlDataProvider
     *
     * @param  string  $url
     * @param  string  $expectType
     * @param  string  $expectId
     * @param  string  $expectOwner
     *
     * @return void
     */
    public function testExtractFromUrl(string $url, string $expectType, string $expectId, string $expectOwner): void
    {
        Flickr::shouldReceive('get')
            ->withAnyArgs()
            ->andReturn((object) ['user' => (object) ['id' => '12345678@N01']]);

        $result = app(UrlExtractor::class)->extract($url);

        $this->assertInstanceOf(FlickrUrl::class, $result);
        $this->assertSame($result->getType(), $expectType);
        $this->assertSame($result->getId(), $expectId);
        $this->assertSame($result->getOwner(), $expectOwner);
    }
}
