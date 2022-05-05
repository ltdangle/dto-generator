<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClass;

use Nette\PhpGenerator\ClassType;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Service\ClassGenerator\AbstractClassGenerator;

/**
 * Generates 'array item' data structure.
 */
class ArrayItemClassGenerator extends AbstractClassGenerator
{
    /** {@inheritdoc} */
    protected function _buildClass(ClassType $classType, ClassEntity $classEntity)
    {
        foreach ($classEntity->getClassProperties() as $classProperty) {
            // add properties
            $classType->addProperty($classProperty->getPropertyName())->setType($classProperty->getPropertyType());
            // add getters and setters
            $this->_addGetter($classProperty, $classType);
            $this->_addSetter($classProperty, $classType);
        }
    }
}
