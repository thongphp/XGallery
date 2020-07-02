<?php

namespace App\Events\Traits;

interface ActivityEvent
{
    public function getActor();

    public function getActorTable(): ?string;

    public function getAction(): string;

    public function getObjectId(): ?string;

    public function getObjectTable(): ?string;

    public function getExtra(): array;

    public function getText(): string;

    public function translate(): string;
}
