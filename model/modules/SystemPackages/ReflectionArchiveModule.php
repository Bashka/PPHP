<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение архива модуля системы.
 * Класс может быть инстанциирован только для архивов модулей с правильной структурой.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class ReflectionArchiveModule extends ReflectionArchive{
  /**
   * @param string $archiveAddress Полный адрес архива компонента.
   * @throws NotExistsException Выбрасывается в случае отсутствия целевого архива.
   * @throws StructureException Выбрасывается в случае нарушения структуры целевого архива.
   */
  public function __construct($archiveAddress){
    parent::__construct($archiveAddress);
    if(empty($this->conf['Component']['type'])){
      throw new StructureException('Нарушение структуры файла конфигурации архива.');
    }
    if($this->conf['Component']['type'] == ReflectionModule::SPECIFIC && $this->archive->statName('Controller.php') === false){
      throw new StructureException('В целевом архиве конкретного модуля отсутствует контроллер.');
    }
  }

  /**
   * Метод возвращает тип модуля.
   * @return string
   */
  public function getType(){
    return $this->conf['Component']['type'];
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
   * Метод определяет, имеет ли модуль ограничения доступа.
   * @return boolean
   */
  public function hasAccess(){
    return !empty($this->conf['Access']);
  }

  /**
   * Метод возвращает ассоциативный массив ограничений доступа модуля.
   * @return array Ассоциативный массив ограничений доступа модуля, имеющий следующую структуру: [имяМетода => [имяРоли, ...], ...].
   */
  public function getAccess(){
    if(!$this->hasAccess()){
      return [];
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
   * Метод распаковывает архив в указанную директорию заменяя файл конфигурации на файл состояния компонента.
   * @param Directory $location Целевая директория.
   * @return FileINI Файл состояния распакованного системного компонента.
   */
  public function expand(Directory $location){
    $state = parent::expand($location);
    $state->Component_type = $this->getType();
    $state->Depending_parent = (string) $this->getParent();
    $state->Depending_children = '';
    if($this->hasAccess()){
      $accesses = $this->getAccess();
      foreach($accesses as $method => $access){
        $state->set($method, implode(',', $access), 'Access');
      }
    }
    $state->rewrite();

    return $state;
  }
}