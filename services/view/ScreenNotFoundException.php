<?php
namespace PPHP\services\view;

/**
 * Выбрасывается в случае, если требуемого экрана не найдено.
 */
class ScreenNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\EnvironmentException{
  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение об отсутствии требуемого экрана.
   * @param string $moduleName Имя родительского модуля.
   * @param string $screenName Имя требуемого экрана.
   * @return static Объект данного класса с предустановленным сообщением.
   */
  public static function getException($moduleName, $screenName){
    return new static('Требуемый экран ['.$moduleName.'::'.$screenName.'] отсутствует в системе.');
  }
}
