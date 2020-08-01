<?php

namespace App\Crawlers\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\CreatesApplication;
use Tests\Traits\FixSQLite;

/**
 * Class TestCase
 * @package App\Crawlers\Tests
 */
abstract class TestCase extends BaseTestCase
{
    protected string $baseUrl = 'http://localhost';

    use CreatesApplication, FixSQLite;
}
