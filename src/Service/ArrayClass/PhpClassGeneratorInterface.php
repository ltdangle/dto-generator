<?php

namespace Sodalto\DtoGenerator\Service\ArrayClass;


use Nette\PhpGenerator\PhpFile;

/**
 * Generates array-like data structure.
 */
interface PhpClassGeneratorInterface
{
    public function generateFile(): PhpFile;
}