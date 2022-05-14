<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ClassGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\PsrPrinter;
use Sodalto\DtoGenerator\Service\ClassGenerator\DTO\ArrayClassGeneratorResult;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

class ArrayClassGenerator
{
    protected NameSpaceResolver $nameSpaceResolver;

    protected string $path = '';
    protected string $wrapperClassName = '';
    protected string $itemClassName = '';
    /** @param Property[] $classProperties */
    protected array $classProperties = [];

    public function __construct(NameSpaceResolver $nameSpaceResolver)
    {
        $this->nameSpaceResolver = $nameSpaceResolver;
    }

    public function writeClasses(): ArrayClassGeneratorResult
    {
        return new ArrayClassGeneratorResult($this->generateItemClass(), $this->generateArrayWrapperClass());
    }

    public function generateArrayWrapperClass(): string
    {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = new PhpNamespace($this->nameSpaceResolver->path2Namespace($this->path));
        $file->addNamespace($namespace);

        $class = $namespace->addClass($this->wrapperClassName);
        $class->addComment("{$this->wrapperClassName} array-like data structure.");

        $items = new Property('items');
        $items->setVisibility('private');
        $items->setType('array');
        $items->setValue([]);
        $items->addComment("@var {$this->itemClassName}[]");
        $class->addMember($items);

        // add getter
        $this->_addGetter($items, $class);
        // add 'adder'
        $method = $class
            ->addMethod('addItem')
            ->addBody('$this->'.$items->getName().'[] = $item;')
            ->setReturnType('void');

        $method->addParameter('item')->setType($namespace->getName().'\\'."$this->itemClassName");

        $printer = new PsrPrinter();
        $classPath = "{$this->getPath()}{$this->getWrapperClassName()}.php";
        file_put_contents($classPath, $printer->printFile($file));

        return $classPath;
    }

    public function generateItemClass(): string
    {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = new PhpNamespace($this->nameSpaceResolver->path2Namespace($this->path));
        $file->addNamespace($namespace);

        $class = $namespace->addClass($this->itemClassName);
        $class->addComment("{$this->wrapperClassName} item.");

        foreach ($this->getClassProperties() as $classProperty) {
            // add properties
            $class->addMember($classProperty);

            // add getters and setters
            $this->_addGetter($classProperty, $class);
            $this->_addSetter($classProperty, $class);
        }

        $printer = new PsrPrinter();
        $classPath = "{$this->getPath()}{$this->itemClassName}.php";
        file_put_contents($classPath, $printer->printFile($file));

        return $classPath;
    }

    private function _addSetter(Property $property, ClassType $classType): void
    {
        $method = $classType
            ->addMethod('set'.ucfirst($property->getName()))
            ->addBody('$this->'.$property->getName().'=$'.$property->getName().';')
            ->setReturnType('void');

        $method->addParameter($property->getName())->setType($property->getType());
    }

    private function _addGetter(Property $property, ClassType $classType): void
    {
        $classType->addMethod('get'.ucfirst($property->getName()))
            ->addBody('return $this->'.$property->getName().';')
            ->setReturnType($property->getType());
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getWrapperClassName(): string
    {
        return $this->wrapperClassName;
    }

    public function setWrapperClassName(string $wrapperClassName): void
    {
        $this->wrapperClassName = $wrapperClassName;
        $this->itemClassName = $wrapperClassName.'Item';
    }

    public function getClassProperties(): array
    {
        return $this->classProperties;
    }

    /** @param Property[] $classProperties */
    public function setClassProperties(array $classProperties): void
    {
        $this->classProperties = $classProperties;
    }
}
