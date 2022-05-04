<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ArrayClass;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use function Psy\sh;

/**
 * Generates array-like data structure.
 */
class ArrayItemClassGenerator implements PhpClassGeneratorInterface
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

    protected function _buildClass(ClassType $classType)
    {
        foreach ($this->classEntity->getClassProperties() as $classProperty) {
            // add properties
            $classType->addProperty($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
            // add getters and setters
            $this->_addGetter($classProperty, $classType);
            $this->_addSetter($classProperty, $classType);
        }

    }

    protected function _addSetter(ClassPropertyEntity $classProperty, ClassType $classType)
    {
        $method=$classType
            ->addMethod('set' . ucfirst($classProperty->getPropertyName()))
            ->addBody('$this->' . $classProperty->getPropertyName() . '=$' . $classProperty->getPropertyName() . ';')
            ->setReturnType($classProperty->getPropertyType());

        $method->addParameter($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
    }

    protected function _addGetter(ClassPropertyEntity $classProperty, ClassType $classType)
    {
        $classType->addMethod('get' . ucfirst($classProperty->getPropertyName()))
            ->addBody('return $this->' . $classProperty->getPropertyName() . ';')
            ->setReturnType($classProperty->getPropertyType());
    }

}