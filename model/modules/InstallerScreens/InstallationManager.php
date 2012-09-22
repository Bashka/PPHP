<?php
namespace PPHP\model\modules\InstallerScreens;

/**
 * Модуль обеспечивает механизмы добавления и удаления экранов.
 */
class InstallationManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод возвращает информацию, необходимую для установки экрана.
   * @param string $archiveAddress Полный адрес до архива экрана.
   * @return array Информация для установки экрана, включающая следующие пункты:
   * - archive - экземпляр класса ZipArchive, открытого на чтение архива экрана;
   * - name - имя экрана;
   * - module - имя родительского модуля;
   * - dir - адрес каталога, в котором должен быть размещен экран;
   * - namespace - корневая область видимости экрана;
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если экран не содержит обязательных компонентов или его архив не найден.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если недостаточно данных для установки экрана.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если экран с данным именем уже установлен в системе.
   */
  protected function getDataScreen($archiveAddress){
    if(!file_exists($archiveAddress)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемого архива экрана не существует в системе.');
    }
    $data = [];
    $zip = new \ZipArchive;
    $data['archive'] = $zip;

    $zip->open($archiveAddress);
    if(!$zip->statName('conf.ini')){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура экрана. Отсутствует описательный файл экрана.');
    }

    // Получение имени экрана.
    $zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerScreens/temp', ['conf.ini']);
    $confFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerScreens/temp/conf.ini');
    $conf = new \PPHP\tools\classes\standard\fileSystem\FileINI($confFile, false);
    if(!$conf->isDataExists('name')){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Нарушена структура описательного файла экрана.');
    }
    $data['name'] = $conf->get('name');

    // Проверка файла разметки экрана.
    if(!$zip->statName($data['name'].'.html')){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура экрана. Отсутствует файл разметки экрана.');
    }

    // Получение родительского модуля экрана.
    if(!$conf->isDataExists('module')){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Нарушена структура описательного файла экрана.');
    }
    $data['module'] = $conf->get('module');

    // Проверка на дублирование экрана.
    $viewRouter = \PPHP\services\view\ViewRouter::getInstance();
    if($viewRouter->isScreenExists($data['module'], $data['name'])){
      $confFile->delete();
      $zip->close();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Экран с заданным именем уже установлен. Дальнейшая установка невозможна.');
    }

    // Определение компонентов экрана.
    $componentsString = ($conf->isDataExists('components'))? $conf->get('components') : false;
    if($componentsString){
      $components = explode(',', $componentsString);
      foreach($components as $component){
        $component = explode('::', $component);
        $moduleComponent = trim($component[0]);
        $screenComponent = trim($component[1]);
        if(!$viewRouter->isScreenExists($moduleComponent, $screenComponent)){
          $confFile->delete();
          $zip->close();
          throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Не найден один из компонентов экрана ('.$moduleComponent.'::'.$screenComponent.'). Дальнейшая установка невозможна.');
        }
      }
    }

    // Определение физического адреса экрана.
    $data['namespace'] = '/PPHP/view/screens/'.$data['module'];
    $data['dir'] = $_SERVER['DOCUMENT_ROOT'] . $data['namespace'];

    // Удаление временных файлов
    $confFile->delete();

    return $data;
  }

  /**
   * Метод устанавливает указанный локальный экран.
   * Для установки экрана необходимы следующие компоненты:
   * - Файл разметки экрана (имяЭкрана.html);
   * - Конфигурационный файл экрана (conf.ini).
   * Конфигурационный файл экрана должен содержать свойство name, определяющее имя экрана, а так же свойство module, определяющее имя родительского модуля.
   * Данный файл так же может включать свойство components, в котором через запятую перечислены обязательные экраны (компоненты данного экрана). Структура этого свойства следующая: имяМодуля::имяЭкрана,имяМодуля::имяЭкрана... Если какого либо из перечисленных экранов-компонентов нет в системе, данный экран не будет установлен.
   * Модуль так же может содержать следующие компоненты:
   * - Файл стиля (имяЭкрана.css);
   * - Файл контроллера экрана (имяЭкрана.js).
   * @param string $archiveAddress Полуный адрес архива модуля.
   * @return string Результаты выполнения установки.
   */
  public function installScreen($archiveAddress){
    $installData = $this->getDataScreen($archiveAddress);
    $viewRouter = \PPHP\services\view\ViewRouter::getInstance();

    // Получение каталога экрана.
    $dirScreen = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($installData['dir']);
    if(!$dirScreen->isExists()){
      $dirScreen->create();
    }

    // Распаковка архива экрана.
    $installData['archive']->extractTo($dirScreen->getAddress(), [$installData['name'].'.html', $installData['name'].'.css', $installData['name'].'.js']);

    // Регистрация экрана в роутере.
    $screenMarking = $installData['namespace'] . '/'.$installData['name'].'.html';
    $viewRouter->setScreen($installData['module'], $installData['name'], $screenMarking);

    // Удаление архива
    $installData['archive']->close();
    $archiveFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($archiveAddress);
    if($archiveFile->isExists()){
      $archiveFile->delete();
    }

    return 'The screen "' . $installData['module'].'::'.$installData['name'] . '" is successfully installed';
  }

  /**
   * Метод устанавливает указанный удаленный экран.
   * @param string $urlScreen URL устанавливаемого экрана.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если загрузка экрана из удаленного хранилища провалилась.
   * @return boolean|string false - если экран не удалось установить.
   */
  public function installScreenURL($urlScreen){
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/InstallerModules/temp/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlScreen));
    fclose($arch);
    if(!file_exists($address)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Невозможно загрузить архив экрана.');
    }
    return $this->installScreen($address);
  }

  /**
   * Метод удаляет указанный экран.
   * @param string $module Родительский модуль удаляемого экрана.
   * @param string $screen Имя удаляемого экрана.
   * @return string Результаты работы метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если требуемого экрана не существует.
   */
  public function uninstallScreen($module, $screen){
    $viewRouter = \PPHP\services\view\viewRouter::getInstance();
    if(!$viewRouter->isScreenExists($module, $screen)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Целевой экран (' . $module.'::'. $screen. ') не найден.');
    }
    $screenMarking = $viewRouter->getScreen($module, $screen);
    $namespaceScreen = substr($screenMarking, 0, strrpos($screenMarking, '/'.$screen.'.html'));

    // Удаление файлов экрана.
    $addressModule = $_SERVER['DOCUMENT_ROOT'] . $namespaceScreen;
    $dirScreen = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($addressModule);
    $dirScreen->getFile($screen.'.html')->delete();
    if($dirScreen->isFileExists($screen.'.css')){
      $dirScreen->getFile($screen.'.css')->delete();
    }
    if($dirScreen->isFileExists($screen.'.js')){
      $dirScreen->getFile($screen.'.js')->delete();
    }

    // Удаление информации о модуле из роутера.
    $viewRouter->removeScreen($module, $screen);
    return 'The module is removed';
  }
}