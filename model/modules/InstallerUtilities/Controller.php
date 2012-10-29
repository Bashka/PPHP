<?php
namespace PPHP\model\modules\InstallerUtilities;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод устанавливает указанную локальную утилиту.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress Полный адрес архива утилиты относительно корневого каталога сайта.
   * @return string Результаты выполнения установки.
   */
  public function installUtility(\PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress){
    $archiveAddress = ($archiveAddress->getIsRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal();
    return Installer::getInstance()->installUtility($_SERVER['DOCUMENT_ROOT'] . $archiveAddress);
  }

  /**
   * Метод устанавливает указанную удаленную утилиту.
   * @param \PPHP\tools\classes\standard\baseType\special\network\URL $urlModule URL адрес устанавливаемого архива модуля.
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installUtilityURL(\PPHP\tools\classes\standard\baseType\special\network\URL $urlModule){
    return Installer::getInstance()->installUtilityURL($urlModule->getVal());
  }

  /**
   * Метод удаляет указанную утилиту.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $utilityName Имя удаляемой утилиты.
   * @return string Результаты работы метода.
   */
  public function uninstallUtility(\PPHP\tools\classes\standard\baseType\special\Name $utilityName){
    return Installer::getInstance()->uninstallUtility($utilityName->getVal());
  }
}
