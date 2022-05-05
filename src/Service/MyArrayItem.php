<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service;

/**
 * MyArray item.
 */
class MyArrayItem
{
    public string $one;

    public function getOne(): string
    {
        return $this->one;
    }

    public function setOne(string $one): void
    {
        $this->one=$one;
    }
}
