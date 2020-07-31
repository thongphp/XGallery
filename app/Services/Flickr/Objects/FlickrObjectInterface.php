<?php

namespace App\Services\Flickr\Objects;

use Illuminate\Support\Collection;

interface FlickrObjectInterface
{
    public function isValid(): bool;

    public function getType(): string;

    public function getUrl(): string;

    public function getId(): string;

    public function getOwner(): string;

    public function getPhotosCount(): int;

    public function getPhotos(): Collection;

    public function getTitle(): string;

    public function getDescription(): ?string;

    public function download();
}
