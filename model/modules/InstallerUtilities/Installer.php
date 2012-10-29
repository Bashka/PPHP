<?php
namespace PPHP\model\modules\InstallerUtilities;

/**
 * Модуль обеспечивает механизмы добавления и удаления утилит.
 */
class Installer implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод возвращает информацию, необходимую для установки утилиты.
   * @param string $archiveAddress Полный адрес до архива утилиты.
   * @throws \PPHP\model\modules\utilities\UtilityNotFoundException Выбрасывается в случае отсутствия утилиты, требуемой для работы.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если утилита не содержит обязательных компонентов или ее архив не найден.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если недостаточно данных для установки утилиты.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если утилита с данным именем уже установлена в системе.
   * @return array Информация для установки утилиты, включающая следующие пункты:
   * - utility - ссылка на контроллер утилиты ConfigInstaller;
   * - name - имя утилиты;
   * - dir - адрес каталога, в котором должена быть размещена утилита;
   * - namespace - корневая область видимости утилиты;
   * - installer - true - если утилита содержит Installer класс, иначе - false;
   * - utilities - массив имен обязательныз для данной утилиты утилит.
   */
  protected function getDataUtility($archiveAddress){
    if(!file_exists($archiveAddress)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемого архива утилиты не существует в системе.');
    }
    $data = [];
    $confUtility = \PPHP\model\modules\CentralController::getUtility('ConfigInstaller');
    if(!$confUtility){
      throw new \PPHP\model\modules\utilities\UtilityNotFoundException('ConfigInstaller');
    }
    $confUtility->open($archiveAddress);

    // Проверка наличия контроллера утилиты.
    if(!$confUtility->isFilesExists(['Controller.php'])){
      $confUtility->close();
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура утилиты.');
    }

    // Получение имени утилиты.
    if(!$confUtility->isDataExists([['utility', 'name']])){
      $confUtility->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Нарушена структура описательного файла утилиты.');
    }
    $data['name'] = $confUtility->getConf()->get('name', 'utility');

    // Проверка на дублирование утилиты.
    $utilitiesRouter = \PPHP\services\utilities\UtilitiesRouter::getInstance();
    if($utilitiesRouter->isUtilityExists($data['name'])){
      $confUtility->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Утилита с заданным именем уже установлена. Дальнейшая установка невозможна.');
    }

    // Проверка наличия обязательных утилит.
    if($confUtility->isDataExists([['utility', 'utilities']])){
      $data['utilities'] = $confUtility->getConf()->get('utilities', 'utility');
      $data['utilities'] = explode(',', $data['utilities']);
      foreach($data['utilities'] as $utility){
        if(!$utilitiesRouter->isUtilityExists($utility)){
          throw new \PPHP\model\modules\utilities\UtilityNotFoundException($utility);
        }
      }
    }

    // Определение namespace утилиты и ее физического адреса.
    $data['dir'] = '/PPHP/model/modules/utilities';
    $data['namespace'] = $data['dir'] . '\\' . $data['name'];
    $data['namespace'] = str_replace('/', '\\', $data['namespace']);
    $data['dir'] = $_SERVER['DOCUMENT_ROOT'] . $data['dir'];

    // Определение инсталятора.
    $data['installer'] = ($confUtility->isFilesExists(['Installer.php']) !== false);

    $data['utility'] = $confUtility;

    return $data;
  }

  /**
   * Метод устанавливает указанную локальную утилиту.
   * Для установки утилиты необходимы следующие компоненты:
   * - Контроллер утилиты (Controller.php);
   * - Конфигурационный файл утилиты (conf.ini).
   * Конфигурационный файл утилиты должен содержать свойство name, определяющее имя утилиты. Так же этот файл может включать следующие необязательные свойства:
   * - utilities - список обязательных утилит.
   * Архив утилиты так же может содержать следующие компоненты:
   * - Внутренний инсталятор утилиты (Installer.php), который представляет собой PHP класс с определенным статичным методом install, содержащим скрипт для постустановки утилиты и настройки системы;
   * @param string $archiveAddress Полуный адрес архива утилиты.
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае возникновения ошибки при выполнении внутреннего инсталятора утилиты.
   * @return string Результаты выполнения установки.
   */
  public function installUtility($archiveAddress){
    $installData = $this->getDataUtility($archiveAddress);
    $utilityRouter = \PPHP\services\utilities\UtilitiesRouter::getInstance();

    // Формирование каталога утилиты.
    $dirUtilities = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($installData['dir'] . '/' . $installData['name']);
    $dirUtilities->create();

    // Распаковка утилиты.
    $installData['utility']->moveFiles($dirUtilities);

    // Регистрация утилиты в роутере.
    $controllerModule = $installData['namespace'] . '\Controller';
    $utilityRouter->setController($installData['name'], $controllerModule::getReflectionClass());

    // Выполнение внутреннего инсталлятора.
    $installResult = '';
    if($installData['installer']){
      $installer = $installData['namespace'] . '\Installer';
      try{
        $installResult = $installer::getInstance()->install();
      }
      catch(\Exception $exc){
        $dirUtilities->delete();
        $utilityRouter->removeController($installData['name']);
        throw $exc;
      }
    }

    // Удаление архива.
    $installData['utility']->close();
    $archiveFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($archiveAddress);
    if($archiveFile->isExists()){
      $archiveFile->delete();
    }

    return 'The utility "' . $installData['name'] . '" is successfully installed. Installer: ' . $installResult;
  }

  /**
   * Метод устанавливает указанную удаленную утилиту.
   * @param string $url URL устанавливаемой утилиты.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если загрузка утилиты из удаленного хранилища провалилась.
   * @throws \Exception
   * @return boolean|string false - если утилиту не удалось установить.
   */
  public function installUtilityURL($url){
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerUtilities/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($url));
    fclose($arch);
    if(!file_exists($address)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Невозможно загрузить архив утилиты.');
    }
    try{
      return $this->installUtility($address);
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
   * Метод удаляет указанную утилиту.
   * @param string $utilityName Имя удаляемой утилиты.
   * @return string Результаты работы метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если требуемой утилиты не существует.
   */
  public function uninstallUtility($utilityName){
    $utilitiesRouter = \PPHP\services\utilities\UtilitiesRouter::getInstance();
    if(!$utilitiesRouter->isUtilityExists($utilityName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Целевая утилита ' . $utilityName . ' не найдена.');
    }
    $utility = $utilitiesRouter->getController($utilityName);
    $namespaceUtility = substr($utility, 0, strrpos($utility, '\Controller'));

    // Удаление информации об утилите из роутера.
    $utilitiesRouter->removeController($utilityName);

    // Деинсталяция утилиты.
    $installerUtility = $namespaceUtility . '\Installer';
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . str_replace('\\', '/', $installerUtility) . '.php')){
      $installerUtility::getInstance()->uninstall();
    }

    // Удаление каталога утилиты.
    $addressUtility = $_SERVER['DOCUMENT_ROOT'] . str_replace('\\', '/', $namespaceUtility);
    $dirModule = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($addressUtility);
    if($dirModule->isExists()){
      $dirModule->delete();
    }

    return 'The utility is removed';
  }
}