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
use App\Models\Traits\HasCover;
use App\Services\Client\HttpClient;
use Illuminate\Support\Facades\Auth;

/**
 * Class Kissgoddess
 * @property string $url
 * @property string $title
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class KissGoddess extends Mongodb implements DownloadableInterface
{
    use HasCover;

    public $collection = 'kissgoddess';

    protected $fillable = ['url', 'title', 'cover', 'images'];

    public const TITLE = 'title';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function download(): bool
    {
        if (empty($this->images)) {
            return false;
        }

        $httpClient = app(HttpClient::class);

        foreach ($this->images as $image) {
            $httpClient->download($image, 'kissgoddess'.DIRECTORY_SEPARATOR.$this->getTitle());
        }

        return true;
    }

    /**
     * Noncompliant@+1
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
                        'Title' => $this->title,
                        'Photos count' => count($this->images),
                    ],
                    'footer' => $this->url,
                ],
            ]
        );

        KissGoddessDownload::dispatch($this);
    }
}
