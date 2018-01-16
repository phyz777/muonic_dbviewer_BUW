<?php

function autoloader($className)
{
    $classPath = 'lib/vendor/';
    foreach (str_split($className, 1) as $c) {
        if ($c === "\\"){
            $c = '/';
        }
        $classPath .= $c;
    }
    $classPath .= '.php';
    include $classPath;
}

spl_autoload_register('autoloader');
