<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Entity;

/**
 * Entity representing class property.
 */
class ClassPropertyEntity
{
    private string $propertyName;
    private string $propertyType;

    public function __construct(string $propertyName, string $propertyType)
    {
        $this->setPropertyName($propertyName);
        $this->setPropertyType($propertyType);
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function setPropertyName(string $propertyName): void
    {
        $this->propertyName = $propertyName;
    }

    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): void
    {
        $this->propertyType = $propertyType;
    }
}
