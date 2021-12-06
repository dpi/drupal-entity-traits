<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use dpi\DrupalPhpunitBootstrap\Utility;

$loader = require __DIR__ . '/../vendor/autoload.php';
assert($loader instanceof ClassLoader);
$dirs = [];
foreach ([
             __DIR__ . '/../vendor/drupal/core/modules',
             __DIR__ . '/../vendor/drupal/core/profiles',
             __DIR__ . '/../vendor/drupal/core/themes',
             __DIR__ . '/../vendor/drupal/date_recur',
         ] as $dir) {
    $dirs = array_merge($dirs, Utility::drupal_phpunit_find_extension_directories($dir));
}

foreach (Utility::drupal_phpunit_get_extension_namespaces($dirs) as $prefix => $paths) {
    $loader->addPsr4($prefix, $paths);
}
