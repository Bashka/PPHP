<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * @var EssenceManager
   */
  protected $essenceManager;

  function __construct(){
    $this->essenceManager = EssenceManager::getInstance()->setDataMapper();
  }

  /**
   * Метод проверяет доступность указанного экрана модуля для текущего пользователя.
   * @param string $module Целевой модуль.
   * @param string $screen Требуемый экран модуля.
   * @return boolean true - если экран доступен, иначе - false.
   */
  public function isResolved($module, $screen){
    return !AccessManager::getInstance()->setEssenceManager()->hasRestriction(\PPHP\model\modules\CentralController::sendParent(\PPHP\model\modules\System\Users\Access\Controller::getReflectionClass(), 'identifyUser'), $module, $screen);
  }

  /**
   * Метод добавляет новую маску видимости для данного экрана.
   * @param string $module Имя целевой модуль.
   * @param string $screen Имя скрываемого экрана.
   * @return integer Идентификатор добавленной маски.
   */
  public function addShade($module, $screen){
    return $this->essenceManager->addShade($module, $screen);
  }

  /**
   * Метод удаляет маску видимости и все связи с ней.
   * @param integer $OID Идентификатор удаляемой маски видимости.
   * @return boolean true - если маска удачно удалена.
   */
  public function deleteShade($OID){
    return $this->essenceManager->deleteShade(Shade::getProxy($OID));
  }

  /**
   * Метод восстанавливает маску видимости по данным ее идентификатора.
   * @param integer $OID Идентификатор восстанавливаемой маски.
   * @return boolean|Shade Восстановленная маска видимости или false - если маска с данным идентификатором отсутствует.
   */
  public function recoverShade($OID){
    return $this->essenceManager->recoverShade($OID);
  }

  /**
   * Метод восстанавливает маску видимости по запрещаемому экрану.
   * @param string $module Целевой модуль.
   * @param string $screen Запрещаемый экран.
   * @return boolean|Shade Восстановленная маска видимости или false - если маска с данным идентификатором отсутствует.
   */
  public function recoverShadeForScreen($module, $screen){
    return $this->essenceManager->recoverShadeForScreen($module, $screen);
  }

  /**
   * Метод возвращает идентификатор маски видимости.
   * @param string $module Целевой модуль.
   * @param string $screen Скрываемый экран.
   * @return boolean|integer Идентификатор маски видимости или false - если данной маски не существует.
   */
  public function getOIDShade($module, $screen){
    $shade = $this->essenceManager->recoverShadeForScreen($module, $screen);
    if($shade){
      return $shade->getOID();
    }
    else{
      return false;
    }
  }

  /**
   * Метод расширяет роль маской доступа.
   * @param integer $roleOID Идентификатор расширяемой роли.
   * @param integer $shadeOID Идентификатор расширяющей маски доступа.
   * @return boolean true - в случае успеха.
   */
  public function expandRole($roleOID, $shadeOID){
    return $this->essenceManager->addLinkageRoleShade(\PPHP\model\modules\System\Users\Access\Role::getProxy($roleOID), Shade::getProxy($shadeOID));
  }

  /**
   * Метод сужает роль маской доступа.
   * @param integer $roleOID Идентификатор сужаемой роли.
   * @param integer $shadeOID Идентификатор отзываемой маски доступа.
   * @return boolean true - в случае успеха.
   */
  public function narrowRole($roleOID, $shadeOID){
    return $this->essenceManager->removeLinkageRoleShade(\PPHP\model\modules\System\Users\Access\Role::getProxy($roleOID), Shade::getProxy($shadeOID));
  }

  /**
   * Метод возвращает множество масок доступа для данного пользователя.
   * @param integer $userOID Идентификатор целевого пользователя.
   * @return Shade[] Множество масок доступа для данного пользователя (proxy).
   */
  public function getShadesUser($userOID){
    $shades = $this->essenceManager->getShadesUser(\PPHP\model\modules\System\Users\User::getProxy($userOID));
    $array = [];
    foreach($shades as $shade){
      $array[] = $shade;
    }
    return $array;
  }

  /**
   * Метод восстанавливает маски доступа, расширяющие данную роль.
   * @param integer $roleOID Идентификатор целевой роли.
   * @return Shade[]  Множество масок доступа, расширющих данную роль (proxy).
   */
  public function getShadesRole($roleOID){
    $shades = $this->essenceManager->getShadesRole(\PPHP\model\modules\System\Users\Access\Role::getProxy($roleOID));
    $array = [];
    foreach($shades as $shade){
      $array[] = $shade;
    }
    return $array;
  }
}

Controller::getReflectionClass()->setMetadata('ParentModule', 'Access');