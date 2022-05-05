<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ArrayClass;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use PhpParser\Node\Name;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use function Psy\sh;

/**
 * Generates array-like data structure.
 */
class ArrayItemClassGenerator
{
    private NameSpaceResolver $nameSpaceResolver;

    public function __construct(NameSpaceResolver $nameSpaceResolver)
    {
        $this->nameSpaceResolver = $nameSpaceResolver;
    }

    public function generateFile(ClassEntity $classEntity)
    {
        $namespace = $this->nameSpaceResolver->path2Namespace($classEntity->getPath());
        $classEntity->setNamespace($namespace);
        $classEntity->setComment('Array item.');
        foreach ($classEntity->getClassProperties() as $classProperty) {
            $classEntity->addClassProperty($classProperty);
        }

        $file = new PhpFile();
        $file->setStrictTypes();

        $namespace = new PhpNamespace($classEntity->getNamespace());

        $file->addNamespace($namespace);

        $class = $namespace->addClass($classEntity->getName());
        $class->addComment($classEntity->getComment());

        $this->_buildClass($class, $classEntity);

        $printer = new PsrPrinter();
        file_put_contents("{$classEntity->getPath()}/{$classEntity->getName()}.php", $printer->printFile($file));
    }

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

    protected function _addSetter(ClassPropertyEntity $classProperty, ClassType $classType)
    {
        $method = $classType
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