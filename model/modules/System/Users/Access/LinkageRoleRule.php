<?php
namespace PPHP\model\modules\System\Users\Access;

/**
 * Ассоциативный класс, связывающий правила доступа с ролями.
 */
class LinkageRoleRule extends \PPHP\tools\patterns\database\LongObject{
  /**
   * Целевая роль.
   * @var Role
   */
  protected $role;

  /**
   * Связываемое правило доступа.
   * @var Rule
   */
  protected $rule;

  protected function getSavedState(){
    return get_object_vars($this);
  }

  /**
   * @return \PPHP\model\modules\System\Users\Access\Rule
   */
  public function getRule(){
    return $this->rule;
  }

  /**
   * @param \PPHP\model\modules\System\Users\Access\Rule $rule
   */
  public function setRule($rule){
    $this->rule = $rule;
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

}

LinkageRoleRule::getReflectionClass()->setMetadata('NameTable', 'Access_RoleRule');
LinkageRoleRule::getReflectionClass()->setMetadata('KeyTable', 'OID');

LinkageRoleRule::getReflectionProperty('role')->setMetadata('NameFieldTable', 'role');
LinkageRoleRule::getReflectionProperty('rule')->setMetadata('NameFieldTable', 'rule');