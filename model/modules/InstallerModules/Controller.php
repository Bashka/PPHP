<?php
namespace PPHP\model\modules\InstallerModules;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод устанавливает указанный локальный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress Полуный адрес архива модуля относительно относительно корневого каталога сайта.
   * @return string Результаты выполнения установки.
   */
  public function installModule(\PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress){
    $archiveAddress = ($archiveAddress->getIsRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal();
    return Installer::getInstance()->installModule($_SERVER['DOCUMENT_ROOT'] . $archiveAddress);
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\network\URL $urlModule URL адрес устанавливаемого архива модуля.
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installModuleURL(\PPHP\tools\classes\standard\baseType\special\network\URL $urlModule){
    return Installer::getInstance()->installModuleURL($urlModule->getVal());
  }

  /**
   * Метод удаляет указанный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $moduleName Имя удаляемого модуля.
   * @return string Результаты работы метода.
   */
  public function uninstallModule(\PPHP\tools\classes\standard\baseType\special\Name $moduleName){
    return Installer::getInstance()->uninstallModule($moduleName->getVal());
  }
}
