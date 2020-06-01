<?php

namespace App\Jobs\Middleware;

use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Facades\Redis;

/**
 * Class RateLimited
 * @package App\Jobs\Middleware
 */
class RateLimited
{
    private string $key;
    private int    $allow;
    private int    $every;
    private int    $block;

    public function __construct(string $key)
    {
        $value = explode(':', config('ratelimit.'.$key, '6:1:2'));
        $this->key = $key;
        $this->allow = $value[0];
        $this->every = $value[1];
        $this->block = $value[2];
    }

    /**
     * @param $job
     * @param $next
     * @throws LimiterTimeoutException
     */
    public function handle($job, $next)
    {
        Redis::throttle($this->key)
            ->allow($this->allow)->every($this->every)->block($this->every)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                $job->release($this->block * 1.5);
            });
    }
}
