<?php

declare(strict_types=1);

namespace MyNamespace;

/**
 * Array item.
 */
class MyTestClass
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
