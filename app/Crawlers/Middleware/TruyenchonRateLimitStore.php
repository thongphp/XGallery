<?php

namespace App\Crawlers\Middleware;

use Illuminate\Support\Facades\Cache;
use Spatie\GuzzleRateLimiterMiddleware\Store;

class TruyenchonRateLimitStore implements Store
{
    public function get(): array
    {
        return Cache::get('truyenchon-limiter', []);
    }

    public function push(int $timestamp)
    {
        Cache::put('truyenchon-limiter', array_merge($this->get(), [$timestamp]));
    }
}
