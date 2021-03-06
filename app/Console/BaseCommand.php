<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console;

use App\Console\Traits\HasProgressBar;
use App\Repositories\CrawlerEndpoints;
use App\Traits\HasObject;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseCommand
 * @package App\Console
 */
class BaseCommand extends Command
{
    use Notifiable;
    use HasProgressBar;
    use HasObject;

    /**
     * Entry point
     */
    public function handle()
    {
        $task = $this->argument('task');
        $this->output->title('<info>Running </info>'.$task);

        if (!method_exists($this, $task)) {
            $this->output->warning('Task '.$task.' not found');
            return false;
        }

        return call_user_func([$this, $task]);
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return mixed|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->completed(parent::execute($input, $output));
    }

    /**
     * @param $status
     */
    protected function completed($status)
    {
        $this->progressBarFinished();

        if ($status) {
            $this->output->newLine(2);
            $this->output->success('Completed');

            return;
        }

        $this->output->error('Failed');
    }

    protected function getEndpoint(string $name): ?\App\Models\CrawlerEndpoints
    {
        /**
         * @var \App\Models\CrawlerEndpoints $endpoint
         */
        if (!$endpoint = app(CrawlerEndpoints::class)->getWorkingItem($name)) {
            return null;
        }

        if ((int) $endpoint->page === 0) {
            $endpoint->page = 1;
        }

        $this->output->note('Endpoint '.$endpoint->url.' with page '.$endpoint->page);

        return $endpoint;
    }
}
