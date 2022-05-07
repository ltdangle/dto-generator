<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ClassGenerator\DTO;

class ArrayClassGeneratorResult
{
    private string $itemClassPath;
    private string $arrayWrapperClassPath;

    public function __construct(string $itemClassPath, string $arrayWrapperClassPath)
    {
        $this->itemClassPath = $itemClassPath;
        $this->arrayWrapperClassPath = $arrayWrapperClassPath;
    }

    public function getItemClassPath(): string
    {
        return $this->itemClassPath;
    }

    public function getArrayWrapperClassPath(): string
    {
        return $this->arrayWrapperClassPath;
    }
}
