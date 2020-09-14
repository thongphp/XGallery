<?php

namespace App\Models;

interface DownloadableInterface
{
    /**
     * @param User|null $author
     */
    public function startDownload(?User $author = null): void;
}
