<?php

spl_autoload_register(function ($class) {

    $name = __DIR__ . '/../src/' . str_replace('fmihel\\report\\', '', $class);

    if (strpos($name, ':') !== false) {
        $name = str_replace('/', '\\', $name);
    } else {
        $name = str_replace('\\', '\/', $name);
    }
    $name .= '.php';

    if ($name && file_exists($name)) {
        include $name;
        return;
    }

});
