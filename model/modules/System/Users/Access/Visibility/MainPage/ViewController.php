<?php
namespace PPHP\model\modules\System\Users\Access\Visibility\MainPage;

class ViewController{
  /**
   * Метод возвращает адрес экрана.
   * @static
   * @param string $module Целевой модуль.
   * @param string $screen Требуемый экран модуля.
   * @throws \PPHP\services\modules\ModuleNotFoundException Выбрасывается в случае, если требуемого экрана не существует.
   * @return string Адрес запрашиваемого экрана.
   */
  public static function getView($module, $screen){
    $viewRouter = \PPHP\services\view\ViewRouter::getInstance();
    if(!$viewRouter->isScreenExists($module, $screen)){
      throw new \PPHP\services\modules\ModuleNotFoundException('Требуемого экрана "' . $module . '.'.$screen.'" не существует.');
    }
    return $viewRouter->getScreen($module, $screen);
  }
}