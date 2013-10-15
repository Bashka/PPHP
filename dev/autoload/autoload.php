<?php
$_SERVER['DOCUMENT_ROOT'] = substr(__DIR__, 0, strpos(__DIR__, 'PPHP')-1);
spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] .'/'. str_replace('\\', '/', $className) . '.php';
});