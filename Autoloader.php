<?php
defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);
spl_autoload_register(function($class){
    $prefix = 'envtool\\';
    $baseDir = ROOT_PATH . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
