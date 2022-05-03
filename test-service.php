<?php

use Composer\Autoload\ClassLoader;
use Nette\PhpGenerator\ClassType;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ArrayItemGenerator;

require_once 'vendor/autoload.php';
$classEntity = new ClassEntity();
$classEntity->setName('MyTestClass');
$classEntity->setNamespace('MyNamespace');
$classEntity->setComment('Array item.');
$classEntity->addClassProperty(new ClassPropertyEntity('one', '\DateTime'));
$classEntity->addClassProperty(new ClassPropertyEntity('two', 'string'));

$service=new ArrayItemGenerator($classEntity);

$file=$service->generateFile();

$printer = new Nette\PhpGenerator\PsrPrinter;

file_put_contents('GeneratedFile.php', $printer->printFile($file));

echo "\n";
echo realpath(Composer\InstalledVersions::getRootPackage()['install_path']);
echo "\n";

$settings = json_decode(file_get_contents('composer.json'),true);
print_r($settings['autoload']['psr-4']);
echo "\n";

/**
 * @var $classLoader ClassLoader
 */
$classLoader=include 'vendor/autoload.php';
print_r($classLoader->getPrefixesPsr4());