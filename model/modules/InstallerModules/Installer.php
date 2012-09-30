<?php
namespace PPHP\model\modules\InstallerModules;

/**
 * Модуль обеспечивает механизмы добавления и удаления модулей.
 */
class Installer implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод возвращает информацию, необходимую для установки модуля.
   * @param string $archiveAddress Полный адрес до архива модуля.
   * @return array Информация для установки модуля, включающая следующие пункты:
   * - archive - экземпляр класса ZipArchive, открытого на чтение архива модуля;
   * - name - имя модуля;
   * - parentModule - имя родительского модуля или false - если модуль не имеет зависимостей;
   * - dir - адрес каталога, в котором должен быть размещен модуль;
   * - namespace - корневая область видимости модуля;
   * - installer - true - если модуль содержит Installer класс, иначе - false.
   * - access - массив, имеющий следующую структуру: [имяМетодаКонтроллера => [имяРоли, имяРоли,...], ...]
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если модуль не содержит обязательных компонентов или его архив не найден.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если недостаточно данных для установки модуля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если модуль с данным именем уже установлен в системе.
   */
  protected function getDataModule($archiveAddress){
    if(!file_exists($archiveAddress)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемого архива модуля не существует в системе.');
    }
    $data = [];
    $zip = new \ZipArchive;
    $data['archive'] = $zip;

    $zip->open($archiveAddress);
    if(!($zip->statName('conf.ini') && $zip->statName('Controller.php'))){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура модуля.');
    }

    // Получение имени модуля.
    $zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp', ['conf.ini']);
    $confFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp/conf.ini');
    $conf = new \PPHP\tools\classes\standard\fileSystem\FileINI($confFile, true);
    if(!$conf->isDataExists('name', 'module')){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Нарушена структура описательного файла модуля.');
    }
    $data['name'] = $conf->get('name', 'module');

    // Проверка на дублирование модуля.
    $moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();
    if($moduleRouter->isModuleExists($data['name'])){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Модуль с заданным именем уже установлен. Дальнейшая установка невозможна.');
    }

    // Определение родительского модуля.
    $data['parentModule'] = ($conf->isDataExists('parent', 'module'))? $conf->get('parent', 'module') : false;

    // Определение namespace модуля и его физического адреса.
    if($data['parentModule']){
      $moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();
      if(!$moduleRouter->isModuleExists($data['parentModule'])){
        $confFile->delete();
        $zip->close();
        throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемого родительского модуля (' . $data['parentModule'] . ') не существует.');
      }
      $data['namespace'] = $moduleRouter->getController($data['parentModule']);
      $data['namespace'] = substr($data['namespace'], 0, strrpos($data['namespace'], '\Controller'));
      $data['dir'] = str_replace('\\', '/', $data['namespace']);
      $data['namespace'] = $data['dir'] . '\\' . $data['name'];
    }
    else{
      $data['dir'] = '/PPHP/model/modules';
      $data['namespace'] = $data['dir'] . '\\' . $data['name'];
    }
    $data['namespace'] = str_replace('/', '\\', $data['namespace']);
    $data['dir'] = $_SERVER['DOCUMENT_ROOT'] . $data['dir'];

    // Определение инсталятора.
    $data['installer'] = ($zip->statName('Installer.php') !== false);

    // Определение прав доступа
    $data['access'] = $conf->getSection('access');

    // Удаление временных файлов
    $confFile->delete();

    return $data;
  }

  /**
   * Метод устанавливает указанный локальный модуль.
   * Для установки модуля необходимы следующие компоненты:
   * - Контроллер модуля (Controller.php);
   * - Конфигурационный файл модуля (conf.ini).
   * Конфигурационный файл модуля должен содержать свойство name, определяющее имя модуля, а так же свойство parent, определяющее имя родительского модуля, если данный модуль является зависимым.
   * Модуль так же может содержать следующие компоненты:
   * - Внутренний инсталятор модуля (Installer.php), который представляет собой PHP класс с определенным статичным методом install, содержащим скрипт для постустановки модуля и настройки системы.
   * @param string $archiveAddress Полуный адрес архива модуля.
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае возникновения ошибки при выполнении внутреннего инсталятора модуля.
   * @return string Результаты выполнения установки.
   */
  public function installModule($archiveAddress){
    $installData = $this->getDataModule($archiveAddress);
    $moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();

    // Формирование каталога модуля.
    $dirModule = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp/' . $installData['name']);
    $dirModule->create();

    // Распаковка модуля.
    $installData['archive']->extractTo($dirModule->getAddress());

    // Перемещение модуля по постоянному адресу.
    $dirModule->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($installData['dir']));

    // Регистрация модуля в роутере.
    $controllerModule = $installData['namespace'] . '\Controller';
    $moduleRouter->setController($installData['name'], $controllerModule::getReflectionClass());

    // Выполнение внутреннего инсталлятора
    if($installData['installer']){
      $installer = $installData['namespace'] . '\Installer';
      $installResult = '';
      try{
        $installResult = $installer::getInstance()->install();
      }
      catch(\Exception $exc){
        $dirModule->delete();
        $moduleRouter->removeController($installData['name']);
        throw $exc;
      }
    }

    // Управление доступом
    if($installData['access'] && $moduleRouter->isModuleExists('Access')){
      $accessManager = $moduleRouter->getController('Access');
      $accessManager = $accessManager::getInstance();
      foreach($installData['access'] as $method => $roles){
        $roles = explode(',', $roles);
        foreach($roles as $role){
          if(($role = $accessManager->getOIDRole($role)) !== false){
            try{
              $rule = $accessManager->addRule($installData['name'], $method);
            }
            catch(\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException $exc){
              $rule = $accessManager->getRuleFromPurpose($installData['name'], $method)->getOID();
            }
            $accessManager->expandRole($role, $rule);
          }
        }
      }
    }

    // Удаление инсталяционных файлов.
    $dirModule->getFile('conf.ini')->delete();

    // Удаление архива
    $installData['archive']->close();
    $archiveFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($archiveAddress);
    if($archiveFile->isExists()){
      $archiveFile->delete();
    }

    return 'The unit "' . $installData['name'] . '" is successfully installed. Installer: '.$installResult;
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param string $urlModule URL устанавливаемого модуля.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если загрузка модуля из удаленного хранилища провалилась.
   * @throws \Exception
   * @return boolean|string false - если модуль не удалось установить.
   */
  public function installModuleURL($urlModule){
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp/0';
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
      $fileArchive = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp/0');
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
    $moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();
    if(!$moduleRouter->isModuleExists($moduleName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Целевой модуль (' . $moduleName . ') не найден.');
    }
    $module = $moduleRouter->getController($moduleName);
    $namespaceModule = substr($module, 0, strrpos($module, '\Controller'));

    // Деинсталяция модуля.
    $installerModule = $namespaceModule . '\Installer';
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . str_replace('\\', '/', $installerModule) . '.php')){
      $installerModule::getInstance()->uninstall();
    }

    // Управление доступом
    if($moduleRouter->isModuleExists('Access')){
      $accessManager = $moduleRouter->getController('Access');
      $accessManager = $accessManager::getInstance();
      $controllerModule = $module::getInstance();
      $reflectControllerModule = $controllerModule::getReflectionClass();
      foreach($controllerModule::getAllReflectionMethods() as $method){
        if($method->isPublic() && !$method->isStatic() && $method->getDeclaringClass()->getName() == $reflectControllerModule->getName()){
          $rule = $accessManager->getRuleFromPurpose($moduleName, $method->getName());
          if($rule){
            $accessManager->deleteRule($rule->getOID());
          }
        }
      }
    }

    // Удаление каталога модуля.
    $addressModule = $_SERVER['DOCUMENT_ROOT'] . str_replace('\\', '/', $namespaceModule);
    $dirModule = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($addressModule);
    if($dirModule->isExists()){
      $dirModule->delete();
    }

    // Удаление информации о модуле из роутера.
    $moduleRouter->removeController($moduleName);
    return 'The module is removed';
  }
}