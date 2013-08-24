<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\tools\classes\standard\baseType\exceptions\EnvironmentException;

/**
 * Выбрасывается в случае, если требуемого системного компонента не найдено.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class SystemComponentNotFoundException extends EnvironmentException{
  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение об отсутствии требуемого модуля.
   * @param string $name Имя требуемого модуля.
   * @return static Объект данного класса с предустановленным сообщением.
   */
  public static function getException($name){
    return new static('Требуемый компонент [' . $name . '] отсутствует в системе.');
  }
}
