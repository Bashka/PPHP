<?php
namespace PPHP\model\modules\System\Users\Access;

/**
 * Менеджер предоставляет механизмы для разграничения прав доступа к интерфейсу контроллеров модулей
 */
class AccessManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var EssenceManager
   */
  protected $essenceManager;

  /**
   * Метод устанавилвает используемый объектом менеджер сущностей.
   * @param null|\PPHP\model\modules\System\Users\Access\EssenceManager $essenceManager Менеджер сущностей или null - если необходимо использовать менеджер по умолчанию.
   * @return static
   */
  public function setEssenceManager(EssenceManager $essenceManager=null){
    if(!empty($essenceManager)){
      $this->essenceManager = $essenceManager;
    }
    elseif(empty($this->essenceManager)){
      $this->essenceManager = EssenceManager::getInstance();
      $this->essenceManager->setDataMapper();
    }
    return $this;
  }

  /**
   * Метод определяет, утсановлено ли ограничение на доступ к данному методу контроллера модуля у данного пользователя.
   * @param \PPHP\model\modules\System\Users\User $user Проверяемый пользователь (proxy).
   * @param string $module Требуемый модуль.
   * @param string $action Требуемый метод контроллера.
   * @return boolean true - если пользователю запрещен доступ к заданному методу, иначе - false.
   */
  public function hasRestriction(\PPHP\model\modules\System\Users\User $user, $module, $action){
    $rules = $this->essenceManager->getRulesUser($user);
    foreach($rules as $rule){
      $rule = $this->essenceManager->recoverRule($rule->getOID());
      if(strtolower($rule->getModule()) == strtolower($module) && strtolower($rule->getAction()) == strtolower($action)){
        return true;
      }
    }
    return false;
  }
}