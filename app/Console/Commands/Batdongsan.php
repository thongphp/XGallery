<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Services\Client\HttpClient;
use Exception;

/**
 * Class Batdongsan
 * @package App\Console\Commands
 */
final class Batdongsan extends BaseCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batdongsan {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from Batdongsan.com.vn';

    /**
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        $client = new HttpClient();
        $client->download('https://file-examples-com.github.io/uploads/2017/10/file_example_JPG_100kB.jpg', 'test');
        exit;
        if (!$endpoint = $this->getEndpoint('Batdongsan')) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\Batdongsan::class)->getItemLinks($endpoint->url.'/p' . $endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($url) {
            \App\Jobs\Batdongsan::dispatch($url);
            $this->progressBarSetInfo($url);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
