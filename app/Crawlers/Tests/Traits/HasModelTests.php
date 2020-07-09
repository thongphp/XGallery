<?php

namespace App\Crawlers\Tests\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HasModelTests
 * @method assertArrayHasKey($field, $properties)
 * @package App\Crawlers\Tests\Traits
 */
trait HasModelTests
{
    protected function assertModelProperties(array $expectedFields, Model $model): void
    {
        $properties = $model->getAttributes();

        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $properties);
        }
    }
}
