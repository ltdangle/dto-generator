<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service;

/**
 * MyArray array-like structure.
 */
class MyArray
{
    public MyArrayItem $items;

    public function getItems(): MyArrayItem
    {
        return $this->items;
    }

    public function setItems(MyArrayItem $items): void
    {
        $this->items=$items;
    }
}
