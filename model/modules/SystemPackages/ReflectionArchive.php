<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\metadata as metadata;

abstract class ReflectionArchive implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Имя файла конфигурации архива компонента.
   */
  const CONF_FILE_NAME = 'conf.ini';

  /**
   * Обрабатываемый архив.
   * @var \ZipArchive
   */
  protected $archive;

  /**
   * Ассоциативный массив, содержащий конфигурацию архива компонента.
   * Файл конфигурации должен иметь следующую структуру:
   * [Component]
   * name=имя
   * version=версия
   * [Depending]*
   * used=используемыйКомпонент,...*
   * ...
   * * - не обязательные компоненты.
   * @var array
   */
  protected $conf;

  /**
   * @param string $archiveAddress Полный адрес архива компонента.
   * @throws NotExistsException Выбрасывается в случае отсутствия целевого архива.
   * @throws StructureException Выбрасывается в случае нарушения структуры целевого архива.
   */
  public function __construct($archiveAddress){
    InvalidArgumentException::verifyType($archiveAddress, 'S');
    $this->archive = new \ZipArchive;
    if(file_exists($archiveAddress)){
      $this->archive->open($archiveAddress);
      if($this->archive->statName(self::CONF_FILE_NAME) === false){
        throw new StructureException('Отсутствует файл конфигурации архива.');
      }
      $this->conf = parse_ini_string($this->archive->getFromName(self::CONF_FILE_NAME), true);
      if(empty($this->conf['Component']['name']) || empty($this->conf['Component']['version'])){
        throw new StructureException('Нарушение структуры файла конфигурации архива.');
      }
    }
    else{
      throw new NotExistsException('Запрашиваемый архив [' . $archiveAddress . '] не найден.');
    }
  }

  /**
   * Метод возвращает имя компонента, содержащегося в архиве.
   * @return string Имя компонента в архиве.
   */
  public function getName(){
    return $this->conf['Component']['name'];
  }

  /**
   * Метод возвращает версию компонента, содержащегося в архиве.
   * @return string Версия компонента в архиве.
   */
  public function getVersion(){
    return $this->conf['Component']['version'];
  }

  /**
   * Метод определяет, имеет ли компонент зависимости.
   * @return boolean true - если хависимости имеются, иначе - false.
   */
  public function hasUsed(){
    return !empty($this->conf['Depending']) && !empty($this->conf['Depending']['used']);
  }

  /**
   * Метод возвращает массив используемых данным компонентом компонентов.
   * @return string[] Массив имен используемых компонентов.
   */
  public function getUsed(){
    if(!$this->hasUsed()){
      return [];
    }

    return array_map(function ($v){
      return trim($v);
    }, explode(',', $this->conf['Depending']['used']));
  }

  /**
   * Метод распаковывает архив в указанную директорию заменяя файл конфигурации на файл состояния компонента.
   * @param Directory $location Целевая директория.
   * @return FileINI Файл состояния распакованного системного компонента.
   */
  public function expand(Directory $location){
    // Извлечение архива
    $this->archive->extractTo($location->getAddress());
    // Формирование файла состояния модуля
    $state = $location->createFile(ReflectionSystemComponent::STATE_FILE_NAME);
    $state = new FileINI($state, true);
    $state->Component_name = $this->getName();
    $state->Component_version = $this->getVersion();
    $state->Depending_userd = implode(',', $this->getUsed());
    $state->Depending_destitute = '';
    $state->rewrite();
    // Удаление файла конфигурации архива
    $location->getFile('conf.ini')->delete();

    return $state;
  }
}