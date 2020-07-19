<?php

namespace App\Traits\Jav;

use App\Objects\Option;
use Illuminate\Support\Collection;

trait HasSortOptions
{
    /**
     * @param Collection $results
     * @param array $selectedOptions
     * @param string $key
     *
     * @return array
     */
    protected function sortOptions(Collection $results, array $selectedOptions, string $key): array
    {
        return $results
            ->map(
                static function ($item) use ($selectedOptions, $key) {
                    return new Option($item->{$key}, $item->{$key}, in_array($item->{$key}, $selectedOptions, true));
                }
            )
            ->sort(
                static function (Option $itemA, Option $itemB) {
                    if ($itemA->isSelected() !== $itemB->isSelected()) {
                        return !$itemA->isSelected();
                    }

                    return $itemA->getText() <=> $itemB->getText();
                }
            )
            ->toArray();
    }
}
