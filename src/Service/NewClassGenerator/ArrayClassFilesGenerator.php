<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\NewClassGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\PsrPrinter;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

class ArrayClassFilesGenerator
{
    protected NameSpaceResolver $nameSpaceResolver;

    protected string $path;
    protected string $className;
    /** @param Property[] $classProperties */
    protected array $classProperties;

    public function __construct(NameSpaceResolver $nameSpaceResolver)
    {
        $this->nameSpaceResolver = $nameSpaceResolver;
    }

    public function generateArrayWrapperClass()
    {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = new PhpNamespace($this->nameSpaceResolver->path2Namespace($this->path));
        $file->addNamespace($namespace);

        $class = $namespace->addClass($this->className);
        $class->addComment("{$this->className} array-like data structure.");

        $items = new Property('items');
        $items->setType('array');
        $items->addComment("@var {$this->className}Item[]");
        $class->addMember($items);

        // add getter
        $this->_addGetter($items, $class);
        // add 'adder'
        $method = $class
            ->addMethod('addItem')
            ->addBody('$this->'.$items->getName().'[] = $item;')
            ->setReturnType('void');

        $method->addParameter('item')->setType($namespace->getName().'\\'."{$this->className}Item");

        $printer = new PsrPrinter();
        file_put_contents("{$this->getPath()}/{$this->getClassName()}.php", $printer->printFile($file));
    }

    public function generateItemClass()
    {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = new PhpNamespace($this->nameSpaceResolver->path2Namespace($this->path));
        $file->addNamespace($namespace);

        $class = $namespace->addClass($this->className.'Item');
        $class->addComment("{$this->className} item.");

        foreach ($this->getClassProperties() as $classProperty) {
            // add properties
            $class->addMember($classProperty);

            // add getters and setters
            $this->_addGetter($classProperty, $class);
            $this->_addSetter($classProperty, $class);
        }

        $printer = new PsrPrinter();

        file_put_contents("{$this->getPath()}/{$this->getClassName()}Item.php", $printer->printFile($file));
    }

    protected function _addSetter(Property $property, ClassType $classType): void
    {
        $method = $classType
            ->addMethod('set'.ucfirst($property->getName()))
            ->addBody('$this->'.$property->getName().'=$'.$property->getName().';')
            ->setReturnType('void');

        $method->addParameter($property->getName())->setType($property->getType());
    }

    protected function _addGetter(Property $property, ClassType $classType): void
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

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getClassProperties(): array
    {
        return $this->classProperties;
    }

    public function setClassProperties(array $classProperties): void
    {
        $this->classProperties = $classProperties;
    }
}
