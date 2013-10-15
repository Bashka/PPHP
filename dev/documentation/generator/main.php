<?php
namespace PPHP\dev\documentation\generator;
use PPHP\tools\patterns\cache\Cache;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

$doc = new \DOMDocument('1.0', 'UTF-8');
$d = new Generator;
$doc->loadXML($d->interpretation());
$doc->save('doc.ctd');