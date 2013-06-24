<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

use PPHP\model\classes\ModuleController;
use PPHP\services\modules\ModuleDuplicationException;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\baseType\special\network\URL;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;

/**
 * Модуль позволяет добавлять и удалять модули в системе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class Controller extends ModuleController{
  /**
   * Метод устанавливает указанный локальный модуль.
   * @param FileSystemAddress $archiveAddress Полный адрес архива модуля относительно корневого каталога системы.
   * @throws NotExistsException Выбрасывается в случае, если целевой архив модуля не найден по указанному адресу.
   * @throws StructureException Выбрасывается в случае, если целевой архив модуля имеет недопустимую структуру.
   * @throws ModuleDuplicationException Выбрасывается в случае, если целевой архив модуля уже установлен в системе.
   * @throws ModuleNotFoundException Выбрасывается в случае, если отсутствует необходимый родительский или один из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты выполнения установки.
   */
  public function installModule(FileSystemAddress $archiveAddress){
    $archiveAddress = $_SERVER['DOCUMENT_ROOT'] . (($archiveAddress->isRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal());
    try{
      $archive = new ReflectionArchiveModule($archiveAddress);
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    if($archive->isDuplication()){
      throw new ModuleDuplicationException('Целевой модуль [' . $archive->getName() . '] уже установлен в системе.');
    }
    if(!$archive->isParent()){
      throw new ModuleNotFoundException('Отсутствует родительский модуль [' . $archive->getParent() . '].');
    }
    if(($use = $archive->isUsed()) !== true){
      throw new ModuleNotFoundException('Отсутствует используемый модуль [' . $use . '].');
    }
    try{
      $archive->addRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    // Дальнейший выброс исключний не предполагается.
    $archive->sayParent();
    $archive->sayUsed();
    $result = $archive->install();
    if(ModulesRouter::getInstance()->hasModule('Access')){
      $archive->addAccess();
    }

    return $result;
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param URL $urlModule URL адрес устанавливаемого архива модуля.
   * @throws NotExistsException Выбрасывается в случае, если целевой архив модуля не найден по указанному адресу.
   * @throws StructureException Выбрасывается в случае, если целевой архив модуля имеет недопустимую структуру.
   * @throws ModuleDuplicationException Выбрасывается в случае, если целевой архив модуля уже установлен в системе.
   * @throws ModuleNotFoundException Выбрасывается в случае, если отсутствует необходимый родительский или один из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты выполнения установки.
   */
  public function installModuleURL(URL $urlModule){
    $urlModule = $urlModule->getVal();
    $address = $_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/InstallerModules/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlModule));
    fclose($arch);
    if(!file_exists($address)){
      throw new NotExistsException('Невозможно загрузить архив модуля [' . $urlModule . '].');
    }
    try{
      return $this->installModule(new FileSystemAddress($address));
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    catch(ModuleDuplicationException $e){
      throw $e;
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
  }

  /**
   * Метод удаляет указанный модуль.
   * @param Name $moduleName Имя удаляемого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия целевого модуля, его родительского модуля или одного из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты работы метода.
   */
  public function uninstallModule(Name $moduleName){
    try{
      $module = new ReflectionModule($moduleName->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      $module->sayParent();
      $module->sayUsed();
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      $module->removeRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $module->uninstall();
    if(ModulesRouter::getInstance()->hasModule('Access')){
      $module->removeAccess(); // Выброс исключений не предполагается
    }
  }
}
