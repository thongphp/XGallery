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
 * Class Kissgoddess
 * @package App\Console\Commands
 */
final class Kissgoddess extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kissgoddess {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from https://kissgoddess.com/gallery/';

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(\App\Models\Kissgoddess::class);
    }
}
