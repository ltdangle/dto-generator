<?php

use Composer\Autoload\ClassLoader;
use Sodalto\DtoGenerator\Entity\ClassEntity;
use Sodalto\DtoGenerator\Entity\ClassPropertyEntity;
use Sodalto\DtoGenerator\Service\ArrayClass\ArrayItemClassGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use function Psy\sh;

require_once 'vendor/autoload.php';

$path = 'src/Service';
$className = 'ArrayItem';

$namespaceResolver = new NameSpaceResolver();
$composer_json = json_decode(file_get_contents('composer.json'), true);

foreach ($composer_json['autoload']['psr-4'] as $namespacePrefix => $namespacePath) {
    $namespaceResolver->addPsr4Mapping($namespacePrefix, $namespacePath);
}

$namespace = $namespaceResolver->path2Namespace($path);

$classEntity = new ClassEntity();
$classEntity->setName($className);
$classEntity->setNamespace($namespace);
$classEntity->setComment('Array item.');
$classEntity->addClassProperty(new ClassPropertyEntity('one', '\DateTime'));
$classEntity->addClassProperty(new ClassPropertyEntity('two', 'string'));

$service = new ArrayItemClassGenerator($classEntity);

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
//print_r($classLoader->getPrefixesPsr4());