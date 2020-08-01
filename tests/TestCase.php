<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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
}
