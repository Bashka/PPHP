<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

/**
 * Модуль позволяет добавлять и удалять модули в системе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод устанавливает указанный локальный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress Полный адрес архива модуля относительно корневого каталога сайта.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если целевой архив модуля не найдне по указанному адресу.
   * @return string Результаты выполнения установки.
   */
  public function installModule(\PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $archiveAddress){
    $archiveAddress = $_SERVER['DOCUMENT_ROOT'].(($archiveAddress->isRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal());
    if(!file_exists($archiveAddress)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемого архива модуля ('.$archiveAddress.') не существует в системе.');
    }
    return InstallationManager::getInstance()->installModule($archiveAddress);
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\network\URL $urlModule URL адрес устанавливаемого архива модуля.
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installModuleURL(\PPHP\tools\classes\standard\baseType\special\network\URL $urlModule){
    return InstallationManager::getInstance()->installModuleURL($urlModule->getVal());
  }

  /**
   * Метод удаляет указанный модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $moduleName Имя удаляемого модуля.
   * @return string Результаты работы метода.
   */
  public function uninstallModule(\PPHP\tools\classes\standard\baseType\special\Name $moduleName){
    return InstallationManager::getInstance()->uninstallModule($moduleName->getVal());
  }
}
