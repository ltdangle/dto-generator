<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator;

/**
 * MyArray item.
 */
class MyArrayItem
{
    /** one prop */
    public string $one;

    /** two prop */
    public string $two;

    public function getOne(): string
    {
        return $this->one;
    }

    public function setOne(string $one): void
    {
        $this->one=$one;
    }

    public function getTwo(): string
    {
        return $this->two;
    }

    public function setTwo(string $two): void
    {
        $this->two=$two;
    }
}
