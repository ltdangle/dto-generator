<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Sodalto\DtoGenerator\Entity\ClassEntity;

/**
 * Generates array-like data structure.
 */
class ArrayDtoGeneratorService
{
    private ClassEntity $classEntity;

    public function __construct(ClassEntity $classEntity)
    {
        $this->classEntity = $classEntity;
    }

    public function generateFile(): PhpFile
    {
        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = new PhpNamespace($this->classEntity->getNamespace());

        $file->addNamespace($namespace);

        $class = $namespace->addClass($this->classEntity->getName());
        $class->addComment($this->classEntity->getComment());

        $this->_buildClass($class);

        return $file;
    }

    private function _buildClass(ClassType $class)
    {
        foreach ($this->classEntity->getClassProperties() as $classProperty) {
            $class->addProperty($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
        }
    }
}