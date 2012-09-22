<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

/**
 * Менеджер предоставляет механизмы управления ролями и правами доступа.
 */
use PPHP\model\modules\System\Users\Access\LinkageUserRole;

class EssenceManager extends \PPHP\tools\classes\standard\storage\database\EssenceManager{
  // User
  /**
   * Метод возвращает множество масок доступа для данного пользователя.
   * @param \PPHP\model\modules\System\Users\User $user Целевой пользователь.
   * @return \SplObjectStorage Множество масок доступа для данного пользователя (proxy).
   */
  public function getShadesUser(\PPHP\model\modules\System\Users\User $user){
    $roles = \PPHP\model\modules\CentralController::sendParent(Controller::getReflectionClass(), 'getRolesUser', $user->getOID());
    $visibilities = new \SplObjectStorage;
    foreach($roles as $role){
      $visibilities->addAll($this->getShadesRole($role));
    }
    $visibilities->rewind();
    return $visibilities;
  }

  /**
   * Метод расширяет роль маской доступа.
   * @param \PPHP\model\modules\System\Users\Access\Role $role Расширяемая роль.
   * @param Shade $shade Расширяющая маска доступа к экрану.
   * @return boolean true - в случае успеха.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если данная роль уже расширена указанной маской доступа.
   */
  public function addLinkageRoleShade(\PPHP\model\modules\System\Users\Access\Role $role, Shade $shade){
    $activeShades = $this->getShadesRole($role);
    // Проверка на дублирование роли.
    foreach($activeShades as $activeShade){
      if($activeShade->getOID() == $shade->getOID()){
        throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Данная роль уже была делегированная пользователю.');
      }
    }
    $linkRoleShade = new LinkageRoleShade;
    $linkRoleShade->setRole($role);
    $linkRoleShade->setShade($shade);
    $this->addEssence($linkRoleShade);
    return true;
  }

  /**
   * Метод сужает роль маской доступа.
   * @param \PPHP\model\modules\System\Users\Access\Role $role Сужаемая роль.
   * @param Shade $shade Отменяемая маска доступа.
   * @return boolean true - в случае успеха.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если заданная роль не была расширенна указанной маской доступа.
   */
  public function removeLinkageRoleShade(\PPHP\model\modules\System\Users\Access\Role $role, Shade $shade){
    try{
      $linkRoleShade = new LinkageRoleShade;
      $this->findEssence($linkRoleShade, ['shade' => $shade, 'role' => $role]);
      $this->removeEssence($linkRoleShade);
      return true;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $e){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Данная роль не была расширена указанной маской.');
    }
  }

  // Shade
  /**
   * Метод восстанавливает маски доступа, расширяющие данную роль.
   * @param \PPHP\model\modules\System\Users\Access\Role $role Целевая роль (proxy).
   * @return \SplObjectStorage Множество омасок доступа, расширющих данную роль (proxy).
   */
  public function getShadesRole(\PPHP\model\modules\System\Users\Access\Role $role){
    $links = $this->recoverFindComponents(LinkageRoleShade::getReflectionClass(), LinkageRoleShade::getReflectionProperty('role'), $role, LinkageRoleShade::getReflectionMethod('getShade'));
    $links->rewind();
    return $links;
  }

  /**
   * Метод добавляет новую маску видимости для данного экрана.
   * @param string $module Имя целевой модуль.
   * @param string $screen Имя скрываемого экрана.
   * @return integer Идентификатор добавленной маски.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если данная маска видимости уже существует.
   */
  public function addShade($module, $screen){
    if($this->recoverShadeForScreen($module, $screen)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Маска видимости для заданного экрана уже существует.');
    }
    $visibility = new Shade();
    $visibility->setModule($module);
    $visibility->setScreen($screen);
    $this->addEssence($visibility);
    return $visibility->getOID();
  }

  /**
   * Метод удаляет маску видимости и все связи с ней.
   * @param Shade $shade Удаляемая маска видимости (proxy).
   * @return boolean true - если маска удачно удалена.
   */
  public function deleteShade(Shade $shade){
    $this->findingRemoveAssoc(LinkageRoleShade::getReflectionClass(), LinkageRoleShade::getReflectionProperty('shade'), $shade);
    $this->removeEssence($shade);
    return true;
  }

  /**
   * Метод восстанавливает маску видимости по данным ее идентификатора.
   * @param integer $OID Идентификатор восстанавливаемой маски.
   * @return boolean|Shade Восстановленная маска видимости или false - если маска с данным идентификатором отсутствует.
   */
  public function recoverShade($OID){
    try{
      return $this->recoverEssence(Shade::getProxy($OID));
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }

  /**
   * Метод восстанавливает маску видимости по запрещаемому экрану.
   * @param string $module Целевой модуль.
   * @param string $screen Запрещаемый экран.
   * @return boolean|Shade Восстановленная маска видимости или false - если маска с данным идентификатором отсутствует.
   */
  public function recoverShadeForScreen($module, $screen){
    $shade = new Shade();
    try{
      $this->findEssence($shade, ['module' => $module, 'screen' => $screen]);
      return $shade;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }
}
