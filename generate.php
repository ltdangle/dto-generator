<?php
require_once 'vendor/autoload.php';

use Composer\InstalledVersions;
use Sodalto\DtoGenerator\Commands\GenerateDtoCommand;
use Sodalto\DtoGenerator\Service\ArrayClass\ArrayItemClassGenerator;
use Sodalto\DtoGenerator\Service\NameSpaceResolver;
use Symfony\Component\Console\Application;

$application = new Application('Generate DTOs for array data structures.', '0.0.1');

$arrayItemClassGenerator = new ArrayItemClassGenerator(new NameSpaceResolver(realpath(InstalledVersions::getRootPackage()['install_path'])));
$application->add(new GenerateDtoCommand($arrayItemClassGenerator));

$application->run();
