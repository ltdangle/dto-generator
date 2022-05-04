<?php

declare(strict_types=1);

namespace Tests;

/**
 * Array item.
 */
class ArrayItem
{
    public \DateTime $one;
    public string $two;

    public function getOne(): \DateTime
    {
        return $this->one;
    }

    public function setOne(\DateTime $one): \DateTime
    {
        $this->one=$one;
    }

    public function getTwo(): string
    {
        return $this->two;
    }

    public function setTwo(string $two): string
    {
        $this->two=$two;
    }
}
