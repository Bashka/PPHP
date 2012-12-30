<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

/**
 * Модуль обеспечивает механизмы добавления и удаления модулей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class InstallationManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Служба маршрутизации модулей.
   * @var \PPHP\services\modules\ModulesRouter
   */
  protected $moduleRouter;

  private function __construct(){
    $this->moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();
  }

  /**
   * Метод оповещает родительский модуль об инсталляции или удалении дочернего модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Инсталлируемый дочерний модуль.
   * @param boolean $isInstall true - если дочерний модуль устанавливается, false - если удаляется.
   */
  protected function notifyParent(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule, $isInstall = true){
    if(!is_null($parent = $reflectionModule->getParent())){
      if($isInstall){
        $this->moduleRouter->getReflectionModule($parent)->addChild($reflectionModule->getName());
      }
      else{
        $this->moduleRouter->getReflectionModule($parent)->removeChild($reflectionModule->getName());
      }
    }
  }

  /**
   * Метод оповещает используемые модули об инсталляции или удалении зависимого модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Инсталлируемый модуль.
   * @param boolean $isInstall true - если дочерний модуль устанавливается, false - если удаляется.
   */
  protected function notifyUsed(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule, $isInstall = true){
    if(!is_null($usedModules = $reflectionModule->getUsed())){
      foreach($usedModules as $usedModule){
        if($isInstall){
          $this->moduleRouter->getReflectionModule($usedModule)->addDestitute($reflectionModule->getName());
        }
        else{
          $this->moduleRouter->getReflectionModule($usedModule)->removeDestitute($reflectionModule->getName());
        }
      }
    }
  }

  /**
   * Метод удаляет дочерние модули данного модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Целевой модуль.
   */
  protected function uninstallChildren(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule){
    if(!is_null($children = $reflectionModule->getChildren())){
      foreach($children as $child){
        $this->uninstallModule($child);
      }
    }
  }

  /**
   * Метод удаляет зависимые от данного модуля модули.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Целевой модуль.
   */
  protected function uninstallDestitute(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule){
    if(!is_null($destitute = $reflectionModule->getDestitute())){
      foreach($destitute as $destituteModule){
        $this->uninstallModule($destituteModule);
      }
    }
  }

  /**
   * Метод устанавливает права доступа к контроллеру устанавливаемого модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Устанавливаемый модуль.
   */
  protected function addAccess(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule){
    if($this->moduleRouter->hasModule('Access')){
      $access = $reflectionModule->getAccess();
      if(count($access) > 0){
        $accessController = $this->moduleRouter->getController('Access');
        foreach($access as $method => $roles){
          foreach($roles as $role){
            // Определения наличия указанной роли.
            if(($role = $accessController->getOIDRole(new \PPHP\tools\classes\standard\baseType\special\Alias($role))) !== false){
              try{
                // Попытка добавление права доступа.
                $rule = $accessController->addRule(new \PPHP\tools\classes\standard\baseType\special\Name($reflectionModule->getName()), new \PPHP\tools\classes\standard\baseType\special\Name($method));
              }
              catch(\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException $exc){
                // В случае наличия добавляемого права доступа, получение его идентификатора.
                $rule = $accessController->getRuleFromPurpose(new \PPHP\tools\classes\standard\baseType\special\Name($reflectionModule->getName()), new \PPHP\tools\classes\standard\baseType\special\Name($method))->getOID();
              }
              // Расширение роли указанным правом доступа.
              $accessController->expandRole(new \PPHP\tools\classes\standard\baseType\Integer($role), new \PPHP\tools\classes\standard\baseType\Integer($rule));
            }
          }
        }
      }
    }
  }

  /**
   * Метод удаляет права доступа у контроллера удаляемого модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule Удаляемый модуль.
   */
  protected function removeAccess(\PPHP\tools\patterns\metadata\reflection\ReflectionModule $reflectionModule){
    if($this->moduleRouter->hasModule('Access')){
      $accessController = $this->moduleRouter->getController('Access');
      $controllerModule = $reflectionModule->getController();
      $reflectControllerModule = $controllerModule::getReflectionClass();
      foreach($controllerModule::getAllReflectionMethods() as $method){
        if($method->isPublic() && !$method->isStatic() && $method->getDeclaringClass()->getName() == $reflectControllerModule->getName()){
          $rule = $accessController->getRuleFromPurpose(new \PPHP\tools\classes\standard\baseType\special\Name($reflectionModule->getName()), new \PPHP\tools\classes\standard\baseType\special\Name($method->getName()));
          if($rule){
            $accessController->deleteRule(new \PPHP\tools\classes\standard\baseType\Integer($rule->getOID()));
          }
        }
      }
    }
  }

  /**
   * Метод возвращает информацию, необходимую для установки модуля.
   * @param string $archiveAddress Полный адрес до архива модуля.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если модуль не содержит обязательных компонентов или не установлены используемые данным модулем модули.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если недостаточно данных для установки модуля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если модуль с данным именем уже установлен в системе.
   * @return array Информация для установки модуля, включающая следующие пункты:
   * - manager - ссылка на менеджер управления архивами компонент системы PPHP\model\modules\SystemPackages\ArchiveManager;
   * - name {string} - имя модуля;
   * - type {string} - тип модуля;
   * - version {string} - версия модуля;
   * - parent {null|string} - имя родительского модуля или null - если модуль не имеет зависимостей;
   * - dir {string} - адрес каталога, в котором должен быть размещен модуль, относительно хранилища модулей;
   * - installer {boolean} - true - если модуль содержит Installer класс, иначе - false;
   * - access {array} - массив, имеющий следующую структуру: [имяМетодаКонтроллера => [имяРоли, имяРоли,...], ...];
   * - used {array|null} - массив имен используемых модулей данным модулем или null - если модуль виртуальный.
   */
  protected function getDataModule($archiveAddress){
    $data = [];
    $archiveManager = new \PPHP\model\modules\SystemPackages\ArchiveManager();
    $archiveManager->open($archiveAddress);

    if(!$archiveManager->isDataExists([['Module', 'name'], ['Module', 'type'], ['Module', 'version']])){
      $archiveManager->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Нарушена структура конфигурационного файла модуля.');
    }

    // Получение свойств модуля.
    $confModule = $archiveManager->getConf();
    $data['name'] = $confModule->get('name', 'Module');
    $data['type'] = $confModule->get('type', 'Module');
    $data['version'] = $confModule->get('version', 'Module');

    // Определение родительского модуля.
    if($archiveManager->isDataExists([['Depending', 'parent']])){
      $data['parent'] = $confModule->get('parent', 'Depending');
    }
    else{
      $data['parent'] = null;
    }

    // Проверка на дублирование модуля.
    if($this->moduleRouter->hasModule($data['name'])){
      $archiveManager->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Модуль с заданным именем уже установлен.');
    }

    if($data['type'] == \PPHP\tools\patterns\metadata\reflection\ReflectionModule::SPECIFIC){
      // Проверка наличия контроллера конкретного модуля.
      if(!$archiveManager->isFilesExists(['Controller.php'])){
        $archiveManager->close();
        throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура модуля. Конкретный модуль должен иметь контроллер.');
      }

      // Проверка наличия используемых модулей.
      $data['used'] = $confModule->get('used', 'Depending');
      $data['used'] = trim((string) $data['used']);
      $data['used'] = ($data['used'] == '')? [] : explode(',', $data['used']);
      foreach($data['used'] as $usedModule){
        if(!$this->moduleRouter->hasModule($usedModule)){
          $archiveManager->close();
          throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемый модуль (' . $usedModule . ') не установлен в системе.');
        }
      }
    }
    else{
      $data['used'] = null;
      // Проверка отсутствия контроллера для виртуального модуля.
      if($archiveManager->isFilesExists(['Controller.php'])){
        $archiveManager->close();
        throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура модуля. Виртуальный модуль не может иметь контроллера.');
      }
    }

    // Определение физического адреса модуля.
    if($data['parent']){
      if(!$this->moduleRouter->hasModule($data['parent'])){
        $archiveManager->close();
        throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемый родительский модуль (' . $data['parent'] . ') не установлен в системе.');
      }
      $data['dir'] = $this->moduleRouter->getModule($data['parent']) . '/' . $data['name'];
    }
    else{
      $data['dir'] = $data['name'];
    }

    // Определение инсталятора.
    $data['installer'] = ($archiveManager->isFilesExists(['Installer.php']) !== false);

    // Определение прав доступа
    $data['access'] = $confModule->getSection('Access');
    if(!$data['access']){
      $data['access'] = [];
    }

    $data['manager'] = $archiveManager;

    return $data;
  }

  /**
   * Метод устанавливает указанный локальный модуль.
   * Для установки модуля необходимы следующие компоненты:
   * - Конфигурационный файл модуля (conf.ini).
   * Конфигурационный файл модуля должен содержать следующие свойства:
   * - name - имя модуля;
   * - version - версия модуля;
   * - type - тип модуля.
   * Так же этот файл может включать следующие необязательные свойства:
   * - parent - определяет имя родительского модуля, если данный модуль является дочерним;
   * - used - список используемых данным модулем модулей.
   * и секции:
   * - access - массив свойств, именами которых являются имена запрещаемых методов контроллера, а значениями, список ролей модуля Access, для которых доступ запрещен.
   * Модуль так же может содержать следующие компоненты:
   * - Внутренний инсталятор модуля (Installer.php), который представляет собой PHP класс с определенным статичным методом install, содержащим скрипт для постустановки модуля и настройки системы.
   * @param string $archiveAddress Полуный адрес архива модуля.
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае возникновения ошибки при выполнении внутреннего инсталятора модуля.
   * @throws \PPHP\tools\classes\special\storage\relationTable\FileException
   * @return string Результаты выполнения установки.
   */
  public function installModule($archiveAddress){
    $installData = $this->getDataModule($archiveAddress);
    $moduleDirAddress = $_SERVER['DOCUMENT_ROOT'] . '/' . \PPHP\services\modules\ModulesRouter::MODULES_DIR . '/' . $installData['dir'];

    // Формирование каталога модуля.
    $dirModule = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($moduleDirAddress);
    $dirModule->create();

    // Распаковка модуля.
    $installData['manager']->moveFiles($dirModule);

    // Формирование файла состояния модуля.
    $stateFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($moduleDirAddress . '/state.ini');
    if($stateFile->isExists()){
      $dirModule->delete();
      throw new \PPHP\tools\classes\special\storage\relationTable\FileException('Нарушена структура архива модуля. Невозможно создать файл состояния модуля.');
    }
    $stateFile->create();
    $stateFile = new \PPHP\tools\classes\standard\fileSystem\FileINI($stateFile, true);
    $stateFile->set('name', $installData['name'], 'Module');
    $stateFile->set('type', $installData['type'], 'Module');
    $stateFile->set('version', $installData['version'], 'Module');
    $stateFile->set('parent', $installData['parent'], 'Depending');
    $stateFile->set('children', '', 'Depending');
    if($installData['type'] == \PPHP\tools\patterns\metadata\reflection\ReflectionModule::SPECIFIC){
      $stateFile->set('used', (!is_null($installData['used']) && count($installData['used']) > 0)? implode(',', $installData['used']) : '', 'Depending');
      $stateFile->set('destitute', '', 'Depending');
      foreach($installData['access'] as $method => $roles){
        $stateFile->set($method, $roles, 'Access');
      }
    }
    $stateFile->rewrite();

    // Регистрация модуля в роутере.
    $this->moduleRouter->addModule($installData['name'], (($installData['parent'])? $installData['parent'] : null));

    $reflectionModule = $this->moduleRouter->getReflectionModule($installData['name']);

    // Выполнение внутреннего инсталлятора
    $installResult = '';
    if($installData['installer']){
      $installer = $reflectionModule->getInstaller();
      try{
        $installResult = $installer->install();
      }
      catch(\Exception $exc){
        $dirModule->delete();
        $this->moduleRouter->removeModule($installData['name']);
        throw $exc;
      }
    }

    // Оповещение родительского модуля.
    $this->notifyParent($reflectionModule, true);

    if($installData['type'] == \PPHP\tools\patterns\metadata\reflection\ReflectionModule::SPECIFIC){
      // Оповещение используемых модулей.
      $this->notifyUsed($reflectionModule, true);

      // Управление доступом
      $this->addAccess($reflectionModule);
    }

    // Удаление архива
    $installData['manager']->close();
    $archiveFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($archiveAddress);
    if($archiveFile->isExists()){
      $archiveFile->delete();
    }

    return 'The unit "' . $installData['name'] . '" is successfully installed. Installer: ' . $installResult;
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param string $urlModule URL устанавливаемого модуля.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если загрузка модуля из удаленного хранилища провалилась.
   * @throws \Exception
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installModuleURL($urlModule){
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/InstallerModules/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlModule));
    fclose($arch);
    if(!file_exists($address)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Невозможно загрузить архив модуля.');
    }
    try{
      return $this->installModule($address);
    }
    catch(\Exception $exc){
      $fileArchive = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($address);
      if($fileArchive->isExists()){
        $fileArchive->delete();
      }
      throw $exc;
    }
  }

  /**
   * Метод удаляет указанный модуль.
   * @param string $moduleName Имя удаляемого модуля.
   * @return string Результаты работы метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если требуемого модуля не существует.
   */
  public function uninstallModule($moduleName){
    if(!$this->moduleRouter->hasModule($moduleName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Целевой модуль (' . $moduleName . ') не найден.');
    }
    $reflectionModule = $this->moduleRouter->getReflectionModule($moduleName);

    // Удаление дочерних модулей.
    $this->uninstallChildren($reflectionModule);

    // Деинсталяция модуля.
    $uninstallInfo = '';
    if(!is_null($installer = $reflectionModule->getInstaller())){
      $uninstallInfo = $installer->uninstall();
    }

    // Удаление зависимых модулей.
    $this->uninstallDestitute($reflectionModule);

    // Управление доступом
    $this->removeAccess($reflectionModule);

    // Оповещение родительского модуля.
    $this->notifyParent($reflectionModule, false);

    // Оповещение используемых модулей.
    $this->notifyUsed($reflectionModule, false);

    // Удаление информации о модуле из роутера.
    $this->moduleRouter->removeModule($moduleName);

    // Удаление каталога модуля.
    $dirModule = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/' . $reflectionModule->getAddress());
    if($dirModule->isExists()){
      $dirModule->delete();
    }

    return 'The module is removed. Installer: ' . $uninstallInfo;
  }
}