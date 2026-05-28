<?php
spl_autoload_register(function($class){
    $file = __DIR__.'/../'.str_replace("\\", '/', $class);
    if(file_exists($file . '.php')){
        require_once($file . '.php');
    }
});