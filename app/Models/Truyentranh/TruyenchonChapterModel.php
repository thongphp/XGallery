<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Truyentranh;

use App\Database\Mongodb;
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;

/**
 * Class TruyenchonChapterModel
 * @property string $storyUrl
 * @property string $chapterUrl
 * @property string $chapter
 * @property string $title
 * @property string $description
 * @property array $images
 * @package App\Models
 */
class TruyenchonChapterModel extends Mongodb
{
    use HasUrl;
    use HasCover;

    public $collection = 'truyenchon_chapters';

    protected $fillable = ['storyUrl', 'chapterUrl', 'chapter', 'title', 'description', 'images'];
}
