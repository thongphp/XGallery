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

use App\Jobs\XiurenDownload;
use Spatie\Url\Url;

/**
 * Class XiurenModel
 *
 * @property string $url
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class Xiuren extends AbstractImagesModel implements DownloadableInterface
{
    public $collection = 'xiuren';

    public const URL = 'url';
    public const IMAGES = 'images';

    protected $fillable = ['url', 'cover', 'images'];

    public function getTitle(): string
    {
        return trim(Url::fromString($this->url)->getPath(), '/');
    }

    /**
     * @param User|null $author
     */
    public function startDownload(?User $author = null): void
    {
        $this->notifyDownload($author);

        XiurenDownload::dispatch($this);
    }
}
