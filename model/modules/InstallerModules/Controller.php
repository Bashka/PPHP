<?php
namespace PPHP\model\modules\InstallerModules;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод устанавливает указанный локальный модуль.
   * @param string $archiveAddress Полуный адрес архива модуля.
   * @return string Результаты выполнения установки.
   */
  public function installModule($archiveAddress){
    return Installer::getInstance()->installModule($archiveAddress);
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param string $urlModule URL устанавливаемого модуля.
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installModuleURL($urlModule){
    return Installer::getInstance()->installModuleURL($urlModule);
  }

  /**
   * Метод удаляет указанный модуль.
   * @param string $moduleName Имя удаляемого модуля.
   * @return string Результаты работы метода.
   */
  public function uninstallModule($moduleName){
    return Installer::getInstance()->uninstallModule($moduleName);
  }
}
