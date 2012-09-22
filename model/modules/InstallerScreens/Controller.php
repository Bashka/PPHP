<?php
namespace PPHP\model\modules\InstallerScreens;

class Controller extends \PPHP\model\classes\ModuleController{

  /**
   * Метод устанавливает указанный локальный модуль.
   * @param string $archiveAddress Полуный адрес архива модуля.
   * @return string Результаты выполнения установки.
   */
  public function installScreen($archiveAddress){
    return InstallationManager::getInstance()->installScreen($archiveAddress);
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param string $urlScreen URL устанавливаемого модуля.
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installScreenURL($urlScreen){
    return InstallationManager::getInstance()->installScreenURL($urlScreen);
  }

  /**
   * Метод удаляет указанный модуль.
   * @param string $module Имя родительского модуля удаляемого экрана.
   * @param string $screen Имя удаляемого экрана.
   * @return string Результаты работы метода.
   */
  public function uninstallScreen($module, $screen){
    return InstallationManager::getInstance()->uninstallScreen($module, $screen);
  }
}
