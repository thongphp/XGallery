<?php

namespace App\Objects;

class Option
{
    private string $text;
    private string $value;
    private bool $isSelected;

    /**
     * @param string $text
     * @param string $value
     * @param bool $isSelected
     */
    public function __construct(string $text, string $value, bool $isSelected)
    {
        $this->text = $text;
        $this->value = $value;
        $this->isSelected = $isSelected;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->isSelected;
    }
}
