<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 *
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use App\Jobs\KissGoddessDownload;

/**
 * Class Kissgoddess
 *
 * @property string $url
 * @property string $title
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class KissGoddess extends AbstractImagesModel implements DownloadableInterface
{
    public $collection = 'kissgoddess';

    protected $fillable = ['url', 'title', 'cover', 'images'];

    public const TITLE = 'title';

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param User|null $author
     */
    public function startDownload(?User $author = null): void
    {
        $this->notifyDownload($author);

        KissGoddessDownload::dispatch($this);
    }
}
