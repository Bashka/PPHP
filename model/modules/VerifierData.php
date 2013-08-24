<?php
namespace PPHP\model\modules;

use PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\patterns\metadata\reflection\ReflectionMethod;

/**
 * Класс, отвечающий за верификацию входящих данных.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules
 */
class VerifierData{
  /**
   * Метод преобразует массив входящих данных (аргументов) так, чтобы они соответствовали требованиям целевого метода.
   * @static
   * @param ReflectionMethod $method Целевой метод.
   * @param string[] $args Передаваемые параметры.
   */
  public static function verifyArgs(ReflectionMethod $method, array &$args){
    $i = 0;
    foreach($args as $k => &$argVal){
      if($argVal === ''){
        unset($args[$k]);
        continue;
      }
      try{
        $verifyClass = $method->getParameter($i++)->getClass();
      }
        // Обработка динамических аргументов
      catch(ComponentClassException $exc){
        $verifyClass = false;
      }
      if($verifyClass){
        $verifyClass = $verifyClass->getName();
        try{
          $argVal = $verifyClass::reestablish($argVal);
        }
        catch(StructureException $e){
          throw new InvalidArgumentException('Недопустимое значение параметра. Ожидается [' . $verifyClass . '] вместо [' . $argVal . '].', 1, $e);
        }
      }
    }
  }
}
