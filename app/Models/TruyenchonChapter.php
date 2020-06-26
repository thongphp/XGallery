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
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;

/**
 * Class TruyenchonChapter
 * @package App\Models
 */
class TruyenchonChapter extends Mongodb
{
    use HasUrl;
    use HasCover;

    public $collection = 'truyenchon_chapters';

    protected $fillable = ['storyUrl', 'chapterUrl', 'chapter'];
}
