<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ClassGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

abstract class AbstractClassGenerator implements ClassGeneratorInterface
{
    protected NameSpaceResolver $nameSpaceResolver;

    /** Child classes must implement this method to build $classType */
    abstract protected function _buildClass(ClassType $classType, ClassEntity $classEntity);

    public function __construct(NameSpaceResolver $nameSpaceResolver)
    {
        $this->nameSpaceResolver = $nameSpaceResolver;
    }

    /** {@inheritdoc} */
    public function generateFile(ClassEntity $classEntity): void
    {
        $namespace = $this->nameSpaceResolver->path2Namespace($classEntity->getPath());
        $classEntity->setNamespace($namespace);
        $classEntity->setClassComment($classEntity->getClassComment());
        foreach ($classEntity->getClassProperties() as $classProperty) {
            $classEntity->addClassProperty($classProperty);
        }

        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = new PhpNamespace($classEntity->getNamespace());

        $file->addNamespace($namespace);

        $class = $namespace->addClass($classEntity->getName());
        $class->addComment($classEntity->getClassComment());

        $this->_buildClass($class, $classEntity);

        $printer = new PsrPrinter();
        file_put_contents("{$classEntity->getPath()}/{$classEntity->getName()}.php", $printer->printFile($file));
    }

    protected function _addSetter(ClassPropertyEntity $classProperty, ClassType $classType)
    {
        $method = $classType
            ->addMethod('set'.ucfirst($classProperty->getPropertyName()))
            ->addBody('$this->'.$classProperty->getPropertyName().'=$'.$classProperty->getPropertyName().';')
            ->setReturnType('void');

        $method->addParameter($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
    }

    protected function _addGetter(ClassPropertyEntity $classProperty, ClassType $classType)
    {
        $classType->addMethod('get'.ucfirst($classProperty->getPropertyName()))
            ->addBody('return $this->'.$classProperty->getPropertyName().';')
            ->setReturnType($classProperty->getPropertyType());
    }
}
