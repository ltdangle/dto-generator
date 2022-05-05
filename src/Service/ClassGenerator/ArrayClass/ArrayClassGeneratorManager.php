<?php

declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClass;

use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Service\ClassGenerator\ClassGeneratorInterface;

class ArrayClassGeneratorManager implements ClassGeneratorInterface
{
    private ArrayItemClassGenerator $arrayItemClassGenerator;
    private ArrayWrapperClassGenerator $arrayWrapperClassGenerator;

    public function __construct(ArrayItemClassGenerator $arrayItemClassGenerator, ArrayWrapperClassGenerator $arrayWrapperClassGenerator)
    {
        $this->arrayItemClassGenerator = $arrayItemClassGenerator;
        $this->arrayWrapperClassGenerator = $arrayWrapperClassGenerator;
    }

    /** {@inheritdoc} */
    public function generateFile(ClassEntity $classEntity): void
    {
        $this->arrayItemClassGenerator->generateFile($classEntity);
        $this->arrayWrapperClassGenerator->generateFile($classEntity);
    }
}
