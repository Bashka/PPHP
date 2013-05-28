<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение свойства класса, расширенное возможностью добавления метаданных.
 * Данный класс является отображением свойства с устойчивым состоянием и возможностью аннотирования.
 * Класс наследует все возможности своего родителя, что позволяет использовать его в контексте родительского класса.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionProperty extends \ReflectionProperty implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Данная реализация позволяет добавлять аннотации в объект из PHPDoc.
   * @param mixed $class
   * @param string $name
   */
  public function __construct($class, $name){
    parent::__construct($class, $name);

    $docs = explode("\n", $this->getDocComment());
    $docs = array_splice($docs, 1, -1);
    foreach($docs as $doc){
      $doc = substr(trim($doc), 2);
      if($doc[0] == '@'){
        $point = strpos($doc, ' ');
        if($point !== false){
          $this->setMetadata(substr($doc, 1, $point-1), substr($doc, $point+1));
        }
        else{
          $this->setMetadata(substr($doc, 1), '');
        }
      }
    }
  }
}
