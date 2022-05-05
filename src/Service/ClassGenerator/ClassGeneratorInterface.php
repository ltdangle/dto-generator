<?php

namespace Sodalto\DtoGenerator\Service\ClassGenerator;

use Sodalto\DtoGenerator\Entity\ClassEntity;

interface ClassGeneratorInterface
{
    /**
     * Generates and writers php class file to the filesystem.
     */
    public function generateFile(ClassEntity $classEntity): void;
}
