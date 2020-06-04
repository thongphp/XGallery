<?php

namespace Tests\Unit\Services\Flickr;

use App\Services\Flickr\Url\FlickrAlbumUrl;
use App\Services\Flickr\Url\FlickrGalleryUrl;
use App\Services\Flickr\Url\FlickrPhotoUrl;
use App\Services\Flickr\Url\FlickrProfileUrl;
use App\Services\Flickr\UrlExtractor;
use PHPUnit\Framework\TestCase;

class UrlExtractorTest extends TestCase
{
    public function testExtractAlbum(): void
    {
        $url = 'https://www.flickr.com/photos/flickr/albums/72157707851154934';
        $urlExtractor = new UrlExtractor();
        $result = $urlExtractor->extract($url);

        $this->assertInstanceOf(FlickrAlbumUrl::class, $result);
        $this->assertSame($result->getType(), 'album');
        $this->assertSame($result->getId(), '72157707851154934');
        $this->assertSame($result->getOwner(), 'flickr');
    }

    public function testExtractGallery(): void
    {
        $url = 'https://www.flickr.com/photos/flickr/galleries/72157714491688536';
        $urlExtractor = new UrlExtractor();
        $result = $urlExtractor->extract($url);

        $this->assertInstanceOf(FlickrGalleryUrl::class, $result);
        $this->assertSame($result->getType(), 'gallery');
        $this->assertSame($result->getId(), '72157714491688536');
        $this->assertSame($result->getOwner(), 'flickr');
    }

    public function testExtractPhoto(): void
    {
        $url = 'https://www.flickr.com/photos/kingnik/49956954471';
        $urlExtractor = new UrlExtractor();
        $result = $urlExtractor->extract($url);

        $this->assertInstanceOf(FlickrPhotoUrl::class, $result);
        $this->assertSame($result->getType(), 'photo');
        $this->assertSame($result->getId(), '49956954471');
        $this->assertSame($result->getOwner(), 'kingnik');
    }

    public function testExtractProfile(): void
    {
        $url = 'https://www.flickr.com/people/flickr/';
        $urlExtractor = new UrlExtractor();
        $result = $urlExtractor->extract($url);

        $this->assertInstanceOf(FlickrProfileUrl::class, $result);
        $this->assertSame($result->getType(), 'profile');
        $this->assertSame($result->getId(), 'flickr');
        $this->assertSame($result->getOwner(), 'flickr');
    }
}
