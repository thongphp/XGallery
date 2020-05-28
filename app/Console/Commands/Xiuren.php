<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands;

use App\Console\BaseCrawlerCommand;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Xiuren
 * @package App\Console\Commands
 */
final class Xiuren extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xiuren {task=fully} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from http://www.xiuren.org/';

    /**
     * @return bool
     */
    public function item(): bool
    {
        if (!$url = $this->getOptionUrl()) {
            return false;
        }

        if (!$itemDetail = $this->getCrawler()->getItemDetail($url)) {
            return false;
        }

        $name = basename($itemDetail->url, '.html');

        foreach ($itemDetail->images as $image) {
            $this->getCrawler()->download($image, 'xiuren'.DIRECTORY_SEPARATOR.$name);
        }

        return true;
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(\App\Models\Xiuren::class);
    }
}
