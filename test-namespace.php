<?php
require_once 'vendor/autoload.php';
$file = new Nette\PhpGenerator\PhpFile;
$file->addComment('This file is auto-generated.');
$file->setStrictTypes(); // adds declare(strict_types=1)

$namespace = new Nette\PhpGenerator\PhpNamespace('Foo');
$namespace->addUse('Bar\AliasedClass');

$class = $namespace->addClass('Demo');
$class->addImplement('Foo\A') // it will simplify to A
->addTrait('Bar\AliasedClass'); // it will simplify to AliasedClass

$method = $class->addMethod('method');
$method->addComment('@return ' . $namespace->simplifyType('Foo\D')); // in comments simplify manually
$method->addParameter('arg')
    ->setType('Bar\OtherClass'); // it will resolve to \Bar\OtherClass
$file->addNamespace($namespace);
echo $file;