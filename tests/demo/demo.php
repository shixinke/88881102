<?php
require_once __DIR__.'/../../Autoloader.php';
$tool = new \envtool\EnvTool(__DIR__.'/.env', __DIR__.'/.env.example', __DIR__.'/env', 'test');
$status = $tool->copy();
if (!$status) {
    echo $tool->getErrorMsg().PHP_EOL;
}