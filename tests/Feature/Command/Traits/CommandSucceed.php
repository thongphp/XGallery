<?php


namespace Tests\Feature\Command\Traits;

/**
 * Trait CommandSucceed
 * @package Tests\Feature\Command\Traits
 */
trait CommandSucceed
{
    public function testCommandSucceed()
    {
        foreach ($this->commands as $command) {
            $this->artisan($command)->assertExitCode(0);
        }
    }
}
