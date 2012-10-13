<?php
namespace PPHP\model\modules;

/**
 * Класс, отвечающий за верификацию входящих данных.
 */
class VerifierData{
  /**
   * Метод преобразует массив входящих данных (аргументов) так, чтобы они соответствовали требованиям целевого метода.
   * @static
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $method
   * @param array $args
   */
  public static function verifyArgs(\PPHP\tools\patterns\metadata\reflection\ReflectionMethod $method, array &$args){
    $i = 0;
    foreach($args as &$argVal){
      try{
        $verifyClass = $method->getParameter($i++)->getClass();
        if($verifyClass){
          $verifyClass = $verifyClass->getName();
          $argVal = new $verifyClass($argVal);
        }
      }
      // Обработка динамических аргументов
      catch(\PPHP\tools\classes\standard\baseType\exceptions\LogicException $exc){}
    }
  }
}
