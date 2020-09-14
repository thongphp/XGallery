<?php

namespace App\Models;

use App\Database\Mongodb;
use App\Facades\UserActivity;
use App\Models\Traits\HasCover;
use App\Services\Client\HttpClient;
use Illuminate\Support\Facades\Auth;

/**
 * Class AbstractImagesModel
 * @property string $_id
 * @property string $url
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
abstract class AbstractImagesModel extends Mongodb
{
    use HasCover;

    abstract public function getTitle(): string;

    public function download(): bool
    {
        if (empty($this->images)) {
            return false;
        }

        $httpClient = app(HttpClient::class);

        foreach ($this->images as $image) {
            $httpClient->download($image, $this->collection.DIRECTORY_SEPARATOR.$this->getTitle());
        }

        return true;
    }

    /**
     * @param User|null $author
     */
    public function notifyDownload(?User $author = null): void
    {
        UserActivity::notify(
            '%s request %s gallery',
            $author ?? Auth::user(),
            'download',
            [
                \App\Models\Core\UserActivity::OBJECT_ID => $this->_id,
                \App\Models\Core\UserActivity::OBJECT_TABLE => $this->getTable(),
                \App\Models\Core\UserActivity::EXTRA => [
                    'title' => $this->getTitle(),
                    'fields' => [
                        'ID' => $this->_id,
                        'Title' => $this->getTitle(),
                        'Photos count' => count($this->images),
                    ],
                    'footer' => $this->url,
                ],
            ]
        );
    }
}
