<?php

namespace Tests\Unit\Services\Flickr;

use App\Facades\FlickrClient;
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
            // Album
            [
                'url' => 'https://www.flickr.com/photos/sta-art/albums/72157714761572487',
                'expectType' => 'album',
                'expectId' => '72157714761572487',
                'expectOwner' => '12345678@N01',
            ],
            [
                'url' => 'https://www.flickr.com/photos/sta-art',
                'expectType' => 'profile',
                'expectId' => 'sta-art',
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
                'expectId' => '72157684206305213',
                'expectOwner' => '92537543@N08',
            ],
            [
                'url' => 'https://www.flickr.com/photos/baraods/albums/93826484219372866?foo=bar',
                'expectType' => 'album',
                'expectId' => '93826484219372866',
                'expectOwner' => '12345678@N01',
            ],
            // Gallery
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
            // Photo
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
            // Profile
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
            [
                'url' => 'https://www.flickr.com/photos/139519108@N02/',
                'expectType' => 'profile',
                'expectId' => '139519108@N02',
                'expectOwner' => '139519108@N02',
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
        FlickrClient::shouldReceive('lookUpUser')
            ->withAnyArgs()
            ->andReturn((object) ['user' => (object) ['id' => '12345678@N01']]);

        $result = app(UrlExtractor::class)->extract($url);

        $this->assertInstanceOf(FlickrUrl::class, $result);
        $this->assertSame($result->getType(), $expectType);
        $this->assertSame($result->getId(), $expectId);
        $this->assertSame($result->getOwner(), $expectOwner);
    }
}
