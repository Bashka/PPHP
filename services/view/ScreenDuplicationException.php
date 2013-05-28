<?php
namespace PPHP\services\view;

/**
 * Выбрасывается при попытке дублирования экрана.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\view
 */
class ScreenDuplicationException extends \PPHP\tools\classes\standard\baseType\exceptions\EnvironmentException{
  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение о наличии в системе указанного экрана.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return static Объект данного класса с предустановленным сообщением.
   */
  public static function getException($moduleName, $screenName){
    return new static('Требуемый экран ['.$moduleName.'::'.$screenName.'] уже присутствует в системе.');
  }
}