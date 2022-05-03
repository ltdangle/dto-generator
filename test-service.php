<?php

use Composer\Autoload\ClassLoader;
use Nette\PhpGenerator\ClassType;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ArrayItemGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;

require_once 'vendor/autoload.php';
$path = 'src/Service';
$className = 'ArrayItem';

$namespaceResolver = new NameSpaceResolver();
$namespaceResolver->addPsr4Mapping("Sodalto\\DtoGenerator\\", "src/");
$namespaceResolver->addPsr4Mapping("Tests\\", "tests/");
$namespace=$namespaceResolver->path2Namespace($path);

$classEntity = new ClassEntity();
$classEntity->setName($className);
$classEntity->setNamespace($namespace);
$classEntity->setComment('Array item.');
$classEntity->addClassProperty(new ClassPropertyEntity('one', '\DateTime'));
$classEntity->addClassProperty(new ClassPropertyEntity('two', 'string'));

$service = new ArrayItemGenerator($classEntity);

$file = $service->generateFile();

$printer = new Nette\PhpGenerator\PsrPrinter;

file_put_contents("$path/$className.php", $printer->printFile($file));


echo "\n";
echo realpath(Composer\InstalledVersions::getRootPackage()['install_path']);
echo "\n";

$composer_json = json_decode(file_get_contents('composer.json'), true);
print_r($composer_json['autoload']['psr-4']);
echo "\n";

/**
 * @var $classLoader ClassLoader
 */
$classLoader = include 'vendor/autoload.php';
print_r($classLoader->getPrefixesPsr4());