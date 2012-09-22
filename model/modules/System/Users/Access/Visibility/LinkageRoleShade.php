<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

/**
 * Ассоциативный класс, связывающий правила доступа с ролями.
 */
class LinkageRoleShade extends \PPHP\tools\patterns\database\LongObject{
  /**
   * Целевая роль.
   * @var \PPHP\model\modules\System\Users\Access\Role
   */
  protected $role;

  /**
   * Связываемая маска видимости.
   * @var Shade
   */
  protected $shade;

  protected function getSavedState(){
    return get_object_vars($this);
  }

  /**
   * @param \PPHP\model\modules\System\Users\Access\Role $role
   */
  public function setRole($role){
    $this->role = $role;
  }

  /**
   * @return \PPHP\model\modules\System\Users\Access\Role
   */
  public function getRole(){
    return $this->role;
  }

  /**
   * @param \PPHP\model\modules\System\Users\Access\Visibility\Shade $shade
   */
  public function setShade($shade){
    $this->shade = $shade;
  }

  /**
   * @return \PPHP\model\modules\System\Users\Access\Visibility\Shade
   */
  public function getShade(){
    return $this->shade;
  }
}

LinkageRoleShade::getReflectionClass()->setMetadata('NameTable', 'Visibility_RoleShade');
LinkageRoleShade::getReflectionClass()->setMetadata('KeyTable', 'OID');

LinkageRoleShade::getReflectionProperty('role')->setMetadata('NameFieldTable', 'role');
LinkageRoleShade::getReflectionProperty('shade')->setMetadata('NameFieldTable', 'shade');