<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

/**
 * Представление запрещенного экрана.
 */
class Shade extends \PPHP\tools\patterns\database\LongObject implements \JsonSerializable{
  /**
   * Имя целевого модуля.
   * @var string
   */
  protected $module;

  /**
   * Имя скрываемого экрана
   * @var string
   */
  protected $screen;

  public function JsonSerialize(){
    return ['OID' => $this->getOID(), 'module' => $this->module, 'screen' => $this->screen];
  }

  protected function getSavedState(){
    return get_object_vars($this);
  }

  /**
   * @param $module
   */
  public function setModule($module){
    $this->module = $module;
  }

  /**
   * @return mixed
   */
  public function getModule(){
    return $this->module;
  }

  /**
   * @param $screen
   */
  public function setScreen($screen){
    $this->screen = $screen;
  }

  /**
   * @return mixed
   */
  public function getScreen(){
    return $this->screen;
  }
}

Shade::getReflectionClass()->setMetadata('NameTable', 'Visibility_Shade');
Shade::getReflectionClass()->setMetadata('KeyTable', 'OID');

Shade::getReflectionProperty('module')->setMetadata('NameFieldTable', 'module');
Shade::getReflectionProperty('screen')->setMetadata('NameFieldTable', 'screen');