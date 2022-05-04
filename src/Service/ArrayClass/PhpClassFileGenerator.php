<?php declare(strict_types=1);

namespace Sodalto\DtoGenerator\Service\ArrayClass;

use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

/**
 * Generates ArrayItemClass php file.
 */
class PhpClassFileGenerator
{
    private NameSpaceResolver $nameSpaceResolver;
    private ClassEntity $classEntity;
    private PhpClassGeneratorInterface $phpClassGenerator;
}