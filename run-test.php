<?php

use Nette\PhpGenerator\Property;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use Sodalto\DtoGenerator\Service\NewClassGenerator\ArrayClassFilesGenerator;

require_once 'vendor/autoload.php';

$generator = new ArrayClassFilesGenerator(buildNameSpaceResolver());
$generator->setPath('src/Service');
$generator->setClassName('MyArray');

$p1 = new Property('one');
$p1->setType('string');

$p2 = new Property('two');
$p2->setType('string');

$generator->setClassProperties([$p1, $p2]);
$generator->generateItemClass();
$generator->generateArrayWrapperClass();

function buildNameSpaceResolver(): NameSpaceResolver
{
    $namespaceResolver = new NameSpaceResolver();
    $composer_json = json_decode(file_get_contents(realpath(\Composer\InstalledVersions::getRootPackage()['install_path']).'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

    foreach ($composer_json['autoload']['psr-4'] as $namespacePrefix => $namespacePath) {
        $namespaceResolver->addPsr4Mapping($namespacePrefix, $namespacePath);
    }

    return $namespaceResolver;
}
