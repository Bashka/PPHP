<?php
namespace PPHP\model\modules\System\Users\Access;

/**
 * Представление запрещающего правила доступа. Наличие данного правила, запрещает пользователям доступ к данному методу контроллера указанного модуля.
 */
class Rule extends \PPHP\tools\patterns\database\LongObject implements \JsonSerializable{
  /**
   * Имя модуля, на которого распространяется правило.
   * @var string
   */
  protected $module;

  /**
   * Имя запрещаемого метода.
   * @var string
   */
  protected $action;

  public function JsonSerialize(){
    return ['OID' => $this->getOID(), 'module' => $this->module, 'action' => $this->action];
  }

  protected function getSavedState(){
    return get_object_vars($this);
  }

  /**
   * @return string
   */
  public function getAction(){
    return $this->action;
  }

  /**
   * @return string
   */
  public function getModule(){
    return $this->module;
  }

  /**
   * @param string $action
   */
  public function setAction($action){
    $this->action = $action;
  }

  /**
   * @param string $module
   */
  public function setModule($module){
    $this->module = $module;
  }
}

Rule::getReflectionClass()->setMetadata('NameTable', 'Access_Rules');
Rule::getReflectionClass()->setMetadata('KeyTable', 'OID');

Rule::getReflectionProperty('module')->setMetadata('NameFieldTable', 'module');
Rule::getReflectionProperty('action')->setMetadata('NameFieldTable', 'action');