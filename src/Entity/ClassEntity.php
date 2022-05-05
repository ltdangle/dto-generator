<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Entity;

/**
 * Entity representing php class.
 */
class ClassEntity
{
    private string $path = '';
    private string $name = '';
    private string $namespace = '';
    private string $classComment = '';

    /**
     * @var ClassPropertyEntity[]
     */
    private array $classProperties = [];

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getClassProperties(): array
    {
        return $this->classProperties;
    }

    public function addClassProperty(ClassPropertyEntity $classProperty)
    {
        $this->classProperties[] = $classProperty;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getClassComment(): string
    {
        return $this->classComment;
    }

    public function setClassComment(string $classComment): void
    {
        $this->classComment = $classComment;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
