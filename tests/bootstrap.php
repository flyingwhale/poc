<?php

require  __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require  __DIR__.'/../vendor/pimple/pimple/lib/Pimple.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->useIncludePath(true);

$namespaces = array(
    'POC' => __DIR__.'/../src',
    'Poc' => __DIR__.'/../src',
    'Monolog' => __DIR__.'/../vendor/monolog/src');

$loader->registerNamespaces($namespaces);


//var_dump($namespaces);

$loader->useIncludePath(true);
$loader->register();


