<?php
namespace PPHP\services\modules;

/**
 * Выбрасывается в случае, если требуемого модуля не найдено.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\modules
 */
class ModuleNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\EnvironmentException{
  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение об отсутствии требуемого модуля.
   * @param string $moduleName Имя требуемого модуля.
   * @return static Объект данного класса с предустановленным сообщением.
   */
  public static function getException($moduleName){
    return new static('Требуемый модуль [' . $moduleName . '] отсутствует в системе.');
  }
}
