<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Entity;

use Nette\PhpGenerator\PhpNamespace;

/**
 * Entity representing php class.
 */
class ClassEntity
{
    private string $name = '';
    private string $comment = '';

    /**
     * @var ClassPropertyEntity[]
     */
    private array $classProperties = [];
    private string $namespace = '';

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

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

}