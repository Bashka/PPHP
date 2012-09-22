<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

/**
 * Менеджер предоставляет механизмы для разграничения прав доступа к экранам модулей.
 */
class AccessManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var EssenceManager
   */
  protected $essenceManager;

  /**
   * Метод устанавилвает используемый объектом менеджер сущностей.
   * @param null|\PPHP\model\modules\System\Users\Access\Visibility\EssenceManager $essenceManager Менеджер сущностей или null - если необходимо использовать менеджер по умолчанию.
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
   * @param string $screen Требуемый экран модуля.
   * @return boolean true - если пользователю запрещен доступ к заданному экрану, иначе - false.
   */
  public function hasRestriction(\PPHP\model\modules\System\Users\User $user, $module, $screen){
    $shades = $this->essenceManager->getShadesUser($user);
    foreach($shades as $shade){
      $shade = $this->essenceManager->recoverShade($shade->getOID());
      if(strtolower($shade->getModule()) == strtolower($module) && strtolower($shade->getScreen()) == strtolower($screen)){
        return true;
      }
    }
    return false;
  }
}