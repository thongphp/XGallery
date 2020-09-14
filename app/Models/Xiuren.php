<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use App\Database\Mongodb;
use App\Facades\UserActivity;
use App\Jobs\KissGoddessDownload;
use App\Jobs\XiurenDownload;
use App\Models\Traits\HasCover;
use App\Services\Client\HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Spatie\Url\Url;

/**
 * Class XiurenModel
 * @property string $url
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class Xiuren extends Mongodb implements DownloadableInterface
{
    use HasCover;

    public $collection = 'xiuren';

    public const URL = 'url';
    public const IMAGES = 'images';

    protected $fillable = ['url', 'cover', 'images'];

    public function getTitle(): string
    {
        return trim(Url::fromString($this->url)->getPath(), '/');
    }

    public function download(): bool
    {
        if (empty($this->images)) {
            return false;
        }

        $httpClient = app(HttpClient::class);

        foreach ($this->images as $image) {
            $httpClient->download($image, 'xiuren'.DIRECTORY_SEPARATOR.$this->getTitle());
        }

        return true;
    }

    /**
     * @param User|null $author
     */
    public function startDownload(?User $author = null): void
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

        XiurenDownload::dispatch($this);
    }
}
