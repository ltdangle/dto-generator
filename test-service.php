<?php

use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ArrayDtoGeneratorService;

require_once 'vendor/autoload.php';
$classEntity = new ClassEntity();
$classEntity->setName('MyTestClass');
$classEntity->setNamespace('MyNamespace');
$classEntity->setComment('Generated file for testing.');
$classEntity->addClassProperty(new ClassPropertyEntity('one', 'string'));
$classEntity->addClassProperty(new ClassPropertyEntity('two', 'string'));

$service=new ArrayDtoGeneratorService($classEntity);

$file=$service->generateFile();

file_put_contents('GeneratedFile.php', $file);