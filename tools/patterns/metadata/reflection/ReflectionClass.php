<?php
namespace PPHP\tools\patterns\metadata\reflection;

use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение класса, расширенное возможностью добавления метаданных.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionClass extends \ReflectionClass implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Данная реализация позволяет добавлять аннотации объекту из его PHPDoc.
   * @param mixed $argument
   */
  public function __construct($argument){
    parent::__construct($argument);
    $docs = explode("\n", $this->getDocComment());
    $docs = array_splice($docs, 1, -1);
    foreach($docs as $doc){
      $doc = substr(trim($doc), 2);
      if($doc[0] == '@'){
        $point = strpos($doc, ' ');
        if($point !== false){
          $this->setMetadata(substr($doc, 1, $point - 1), substr($doc, $point + 1));
        }
        else{
          $this->setMetadata(substr($doc, 1), '');
        }
      }
    }
  }

  /**
   * Метод возвращает документацию компонента.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionDoc Документация компонента.
   */
  public function getDoc(){
    return new ReflectionDoc($this->getDocComment());
  }
}
