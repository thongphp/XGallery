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
    protected function testModelProperties(array $expectedFields, Model $model)
    {
        $properties = $model->getAttributes();

        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $properties);
        }
    }
}
