<?php
namespace PPHP\model\modules\System\Users\Access\Visibility\MainPage;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод возвращает физический адрес запрашиваемого экрана.
   * @param string $module Целевой модуль.
   * @param string $screen Запрашиваемый экран модуля.
   * @throws \PPHP\model\modules\AccessException Выбрасывается в случае, если доступ к запрашиваемому экрану запрещен.
   * @return string Физический адрес экрана.
   */
  public function getScreen($module, $screen){
    if(!\PPHP\model\modules\CentralController::sendParent($this->getReflectionClass(), 'isResolved', $module, $screen)){
      throw new \PPHP\model\modules\AccessException('Доступ к данному экрану запрещен.');
    }
    return ViewController::getView($module, $screen);
  }
}

Controller::getReflectionClass()->setMetadata('ParentModule', 'Visibility');