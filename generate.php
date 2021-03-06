<?php

require_once 'vendor/autoload.php';

use Sodalto\DtoGenerator\Commands\GenerateArrayClassCommand;
use Sodalto\DtoGenerator\Service\ClassGenerator\ArrayClassGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use Symfony\Component\Console\Application;

$application = new Application('Generate DTOs for array data structures.', '0.0.1');

$classGenerator = new ArrayClassGenerator(buildNameSpaceResolver());
$application->add(new GenerateArrayClassCommand($classGenerator));
$application->run();

function buildNameSpaceResolver(): NameSpaceResolver
{
    $namespaceResolver = new NameSpaceResolver();
    $composer_json = json_decode(file_get_contents(realpath(\Composer\InstalledVersions::getRootPackage()['install_path']).'/composer.json'), true, 512, JSON_THROW_ON_ERROR);

    foreach ($composer_json['autoload']['psr-4'] as $namespacePrefix => $namespacePath) {
        $namespaceResolver->addPsr4Mapping($namespacePrefix, $namespacePath);
    }

    return $namespaceResolver;
}
