<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Tests\Traits\CreatesApplication;
use Tests\Traits\FixSQLite;

abstract class TestCase extends BaseTestCase
{
    protected string $baseUrl = 'http://localhost';

    use CreatesApplication, FixSQLite;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->hotfixSqlite();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    protected function tearDown(): void
    {
        Artisan::call('migrate:reset');

        parent::tearDown();
    }
}
