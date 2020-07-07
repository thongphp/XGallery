<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CrawlerEndpoints
 * @property string $crawler
 * @property string $url
 * @property int failed
 * @property int $page
 * @package App\Models
 */
class CrawlerEndpoints extends Model
{
    public function fail(): CrawlerEndpoints
    {
        $this->failed = (int) $this->failed + 1;

        // Max try
        if ($this->failed !== 5) {
            $this->page = (int) $this->page + 1;
            return $this;
        }

        $this->page = 1;
        $this->failed = null;

        return $this;
    }

    public function succeed()
    {
        $this->page = (int) $this->page + 1;
        $this->failed = null;
        return $this;
    }
}
