<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение инсталлируемого архива.
 * Данный класс является отражением инсталлируемого архива.
 * Класс может быть инстанциирован только для инсталляционных архивов модулей.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class ReflectionArchiveModule implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Обрабатываемый архив.
   * @var \ZipArchive
   */
  protected $archive;

  /**
   * Ассоциативный массив конфигурации модуля.
   * Файл конфигурации должен иметь следующую структуру:
   * [Module]
   * name=имяМодуля
   * version=версияМодуля
   * type=specific|virtual
   * [Depending]*
   * used=используемыйМодуль,...*
   * parent=родительскийМодуль*
   * [Access]*
   * имяЗапрещаемогоМетода=роль,...
   * ...
   * * - не обязательные компоненты.
   * @var array
   */
  protected $conf;

  /**
   * @param string $archiveAddress Полный адрес до инсталляционного архива.
   * @throws NotExistsException Выбрасывается в случае отсутствия целевого архива.
   * @throws StructureException Выбрасывается в случае нарушения структуры целевого архива.
   */
  public function __construct($archiveAddress){
    InvalidArgumentException::verifyType($archiveAddress, 'S');
    $this->archive = new \ZipArchive;
    if(file_exists($archiveAddress)){
      $this->archive->open($archiveAddress);
      if($this->archive->statName('conf.ini') === false){
        throw new StructureException('Отсутствует файл конфигурации инсталлируемого архива.');
      }
      $this->conf = parse_ini_string($this->archive->getFromName('conf.ini'), true);
      if(empty($this->conf['Module']['name']) || empty($this->conf['Module']['version']) || empty($this->conf['Module']['type'])){
        throw new StructureException('Нарушение структуры файла конфигурации инсталлируемого архива.');
      }
      if($this->conf['Module']['type'] == ReflectionModule::SPECIFIC && $this->archive->statName('Controller.php') === false){
        throw new StructureException('В целевом инсталляционном архиве конкретного модуля отсутствует контроллер.');
      }
    }
    else{
      throw new NotExistsException('Запрашиваемый архив [' . $archiveAddress . '] не найден.');
    }
  }

  /**
   * Метод возвращает имя модуля.
   * @return string
   */
  public function getName(){
    return $this->conf['Module']['name'];
  }

  /**
   * Метод возвращает версию модуля.
   * @return string
   */
  public function getVersion(){
    return $this->conf['Module']['version'];
  }

  /**
   * Метод возвращает тип модуля.
   * @return string
   */
  public function getType(){
    return $this->conf['Module']['type'];
  }

  /**
   * Метод определяет, имеет ли модуль родителя.
   * @return boolean
   */
  public function hasParent(){
    return !empty($this->conf['Depending']) && !empty($this->conf['Depending']['parent']);
  }

  /**
   * Метод возвращает имя родительского модуля.
   * @return boolean|string Имя родительского модуля или false - если модуль не имеет родителя.
   */
  public function getParent(){
    if(!$this->hasParent()){
      return false;
    }

    return trim($this->conf['Depending']['parent']);
  }

  /**
   * Метод определяет, имеет ли модуль зависимости.
   * @return boolean
   */
  public function hasUsed(){
    return !empty($this->conf['Depending']) && !empty($this->conf['Depending']['used']);
  }

  /**
   * Метод возвращает список имен используемых модулей.
   * @return string[]|boolean Список имен используемых модулей или false - если модуль не имеет зависимостей.
   */
  public function getUsed(){
    if(!$this->hasUsed()){
      return false;
    }

    return array_map(function ($v){
      return trim($v);
    }, explode(',', $this->conf['Depending']['used']));
  }

  /**
   * Метод определяет, имеет ли модуль ограничения доступа.
   * @return boolean
   */
  public function hasAccess(){
    return !empty($this->conf['Access']);
  }

  /**
   * Метод возвращает ассоциативный массив ограничений доступа модуля.
   * @return array|boolean Ассоциативный массив ограничений доступа модуля, имеющий следующую структуру: [имяМетода => [имяРоли, ...], ...] - или false - если модуль не имеет ограничений доступа.
   */
  public function getAccess(){
    if(!$this->hasAccess()){
      return false;
    }
    $accesses = $this->conf['Access'];
    foreach($accesses as &$access){
      $access = explode(',', $access);
    }

    return $accesses;
  }

  /**
   * Метод определяет, имеет ли модуль внутренний инсталлятор.
   * @return boolean
   */
  public function hasInstaller(){
    return !($this->archive->statName('Installer.php') === false);
  }

  /**
   * Метод распаковывает инсталляционный архив в указанную директорию заменяя файл конфигурации на файл состояния модуля.
   * Метод автоматически создает корневой каталог модуля, имя которого совпадает с именем самого модуля.
   * @param Directory $location Целевая директория.
   * @throws DuplicationException Выбрасывается в случае, если в целевом каталоге уже существует каталог с именем модуля.
   * @return Directory Корневой каталог модуля
   */
  public function expand(Directory $location){
    $moduleName = $this->getName();
    if($location->isDirExists($moduleName)){
      throw new DuplicationException('Невозможно распаковать инсталляционный архив модуля [' . $moduleName . '] в виду наличия аналогичного каталога.');
    }
    // Создание корневой директории модуля
    $dir = $location->createDir($moduleName);
    // Извлечение архива
    $this->archive->extractTo($dir->getAddress());
    // Формирование файла состояния модуля
    $stateFile = $dir->createFile('state.ini');
    $stateFile = new FileINI($stateFile, true);
    $stateFile->Module_name = $this->getName();
    $stateFile->Module_version = $this->getVersion();
    $stateFile->Module_type = $this->getType();
    $stateFile->Depending_parent = (string) $this->getParent();
    if($this->hasUsed()){
      $stateFile->Depending_userd = implode(',', $this->getUsed());
    }
    else{
      $stateFile->Depending_userd = '';
    }
    $stateFile->Depending_children = '';
    $stateFile->Depending_destitute = '';
    if($this->hasAccess()){
      $accesses = $this->getAccess();
      foreach($accesses as $method => $access){
        $stateFile->set($method, implode(',', $access), 'Access');
      }
    }
    $stateFile->rewrite();

    // Удаление файла конфигурации архива
    $dir->getFile('conf.ini')->delete();

    return $dir;
  }
}