<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator;

/**
 * MyArray array-like data structure.
 */
class MyArray
{
    /** @var MyArrayItem[] */
    public array $items;

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(MyArrayItem $item): void
    {
        $this->items[] = $item;
    }
}
