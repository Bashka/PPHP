<?php
namespace PPHP\services\modules;

/**
 * Выбрасывается при попытке дублирования модуля.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\modules
 */
class ModuleDuplicationException extends \PPHP\tools\classes\standard\baseType\exceptions\EnvironmentException{
  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение о наличии в системе указанного модуля.
   * @param string $moduleName Имя модуля.
   * @return static Объект данного класса с предустановленным сообщением.
   */
  public static function getException($moduleName){
    return new static('Требуемый модуль ['.$moduleName.'] уже присутствует в системе.');
  }
}