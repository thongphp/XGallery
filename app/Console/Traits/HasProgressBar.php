<?php

namespace App\Console\Traits;

use Symfony\Component\Console\Helper\ProgressBar;

trait HasProgressBar
{
    protected string $progressBarFormat = " %current%/%max% %message%"
    .PHP_EOL
    ." %step%/%steps% URLs [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%"
    .PHP_EOL." %info% [%status%]";

    protected string $progressBarFormatSingle = " %current%/%max% %message%"
    .PHP_EOL
    ." [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%"
    .PHP_EOL." %info% [%status%]";

    protected ProgressBar $progressBar;

    private int $progressBarStep = 0;

    /**
     * @param  int  $max
     * @param  bool  $isSingle
     * @return ProgressBar
     */
    protected function progressBarInit($max = 0, bool $isSingle = true): ProgressBar
    {
        $this->progressBar = $this->output->createProgressBar($max);
        $this->progressBar->setFormat($isSingle ? $this->progressBarFormatSingle : $this->progressBarFormat);
        $this->progressBar->setMessage('Steps', 'message');
        $this->progressBar->setMessage(0, 'steps');
        $this->progressBar->setMessage(0, 'step');
        $this->progressBar->setMessage('', 'info');
        $this->progressBar->setMessage('', 'status');

        return $this->progressBar;
    }

    protected function progressBarSetCurrent($value)
    {
        $this->progressBarSetMessage($value, 'current');
    }

    protected function progressBarSetMessage(string $message, string $name = 'message')
    {
        $this->progressBar->setMessage($message, $name);
    }

    protected function progressBarSetMax($value)
    {
        $this->progressBar->setMaxSteps($value);
    }

    protected function progressBarSetStep($value)
    {
        $this->progressBarStep = (int) $value;
        $this->progressBarSetMessage($this->progressBarStep, 'step');
    }

    protected function progressBarAdvanceStep()
    {
        $this->progressBarStep++;
        $this->progressBarSetMessage($this->progressBarStep, 'step');
    }

    protected function progressBarSetSteps($value)
    {
        $this->progressBarSetMessage($value, 'steps');
        $this->progressBarStep = 0;
    }

    protected function progressBarSetInfo($value)
    {
        $this->progressBarSetMessage($value, 'info');
    }

    protected function progressBarSetStatus($value)
    {
        switch ($value) {
            case 'QUEUED':
                $value = '<fg=yellow;options=bold>'.$value.'</>';
                break;
            case 'COMPLETED':
                $value = '<fg=green;options=bold>'.$value.'</>';
                break;
            default:
        }
        $this->progressBarSetMessage($value, 'status');
    }

    protected function progressBarFinished()
    {
        if (isset($this->progressBar)) {
            $this->progressBar->finish();
        }
    }
}
