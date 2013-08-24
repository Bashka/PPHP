<?php
namespace PPHP\dev\documentation\generator;
use PPHP\tools\patterns\cache\Cache;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

$_SERVER['DOCUMENT_ROOT'] = '/var/www';
spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

$doc = new \DOMDocument('1.0', 'UTF-8');
$d = new Generator;
$doc->loadXML($d->interpretation());
$doc->save('doc.ctd');