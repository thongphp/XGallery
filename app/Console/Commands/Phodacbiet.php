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
final class Phodacbiet extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phodacbiet {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from https://phodacbiet.info';

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(\App\Models\Phodacbiet::class);
    }
}
