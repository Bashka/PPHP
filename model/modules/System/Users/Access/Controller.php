<?php
namespace PPHP\model\modules\System\Users\Access;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * @var EssenceManager
   */
  protected $essenceManager;

  function __construct(){
    $this->essenceManager = EssenceManager::getInstance()->setDataMapper();
  }

  /**
   * Метод проверяет доступность указанного метода модуля для текущего пользователя.
   * @param string $module Целевой модуль.
   * @param string $action Требуемый метод модуля.
   * @return boolean true - если метод доступен, иначе - false.
   */
  public function isResolved($module, $action){
    return !AccessManager::getInstance()->setEssenceManager()->hasRestriction(\PPHP\model\modules\CentralController::sendParent($this->getReflectionClass(), 'identifyUser'), $module, $action);
  }

  /**
   * Метод добавляет новую роль.
   * @param string $name Имя добавляемой роли.
   * @return integer Идентификатор добавленной роли.
   */
  public function addRole($name){
    return $this->essenceManager->addRole($name);
  }

  /**
   * Метод удаляет роль по ее идентификатору.
   * @param integer $OID Идентификатора удаляемой роли.
   * @return boolean true - если роль успешно удалена.
   */
  public function deleteRole($OID){
    return $this->essenceManager->deleteRole($this->essenceManager->recoverRole($OID));
  }

  /**
   * Метод удаляет роль по ее имени.
   * @param string $name Имя удаляемой роли.
   * @return boolean true - если роль успешно удалена.
   */
  public function deleteRoleFromName($name){
    return $this->essenceManager->deleteRole($this->essenceManager->recoverRoleFromName($name));
  }

  /**
   * Метод возвращает роль по ее имени.
   * @param string $name Имя требуемой роли.
   * @return Role|boolean Восстановленная роль или false - если данной роли не существует.
   */
  public function getRole($name){
    return $this->essenceManager->recoverRoleFromName($name);
  }

  /**
   * Метод возвращает роль по ее идентификатору.
   * @param integer $OID Идентификатор роли.
   * @return Role|boolean Восстановленная роль или false - если данной роли не существует.
   */
  public function getRoleFromOID($OID){
    return $this->essenceManager->recoverRole($OID);
  }

  /**
   * Метод возвращает идентификатор роли по ее имени.
   * @param string $name Имя роли.
   * @return boolean|integer Идентификатор роли или false - если роли с данным именем не существует.
   */
  public function getOIDRole($name){
    $role = $this->essenceManager->recoverRoleFromName($name);
    if(!$role){
      return false;
    }
    return $role->getOID();
  }

  /**
   * Метод переименовывает роль.
   * @param integer $OID Идентификатор переименовываемой роли.
   * @param string $newName Новое имя роли.
   * @return boolean true - если роль удачно переименована.
   */
  public function renameRole($OID, $newName){
    $role = $this->essenceManager->recoverRole($OID);
    if(!$role){
      return false;
    }
    return $this->essenceManager->renameRole($role, $newName);
  }

  /**
   * Метод переименовывает роль по данным ее старого имени.
   * @param string $name Старое имя роли.
   * @param string $newName Новое имя роли.
   * @return boolean true - если роль удачно переименована.
   */
  public function renameRoleFromName($name, $newName){
    $role = $this->essenceManager->recoverRoleFromName($name);
    if(!$role){
      return false;
    }
    return $this->essenceManager->renameRole($role, $newName);
  }

  /**
   * Метод делегирует роль пользователю.
   * @param integer $userOID Идентификатор пользователя.
   * @param integer $roleOID Идентификатор роли.
   * @return boolean true - если роль делегирована успешно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function delegateRole($userOID, $roleOID){
    $userOID = (int) $userOID;
    $roleOID = (int) $roleOID;
    if($userOID < 1 || $roleOID < 1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Неверный тип аргумента, ожидается integer больший 0.');
    }
    $user = \PPHP\model\modules\System\Users\User::getProxy($userOID);
    $role = Role::getProxy($roleOID);
    return $this->essenceManager->addLinkageUserRole($user, $role);
  }

  /**
   * Метод делегирует роль пользователю по ее имени.
   * @param integer $userOID Идентификатор пользователя.
   * @param string $roleName Имя роли.
   * @return boolean true - если роль делегирована успешно.
   */
  public function delegateRoleFromName($userOID, $roleName){
    $user = \PPHP\model\modules\System\Users\User::getProxy($userOID);
    $role = $this->essenceManager->recoverRoleFromName($roleName);
    return $this->essenceManager->addLinkageUserRole($user, $role);
  }

  /**
   * Метод отзывает роль у пользователя.
   * @param integer $userOID Идентификатор пользователя.
   * @param integer $roleOID Идентификатор роли.
   * @return boolean true - если роль отозвана успешно.
   */
  public function responseRole($userOID, $roleOID){
    $user = \PPHP\model\modules\System\Users\User::getProxy($userOID);
    $role = Role::getProxy($roleOID);
    return $this->essenceManager->removeLinkageUserRole($user, $role);
  }

  /**
   * Метод отзывает роль у пользователя по ее имени.
   * @param integer $userOID Идентификатор пользователя.
   * @param string $roleName Имя роли.
   * @return boolean true - если роль отозвана успешно.
   */
  public function responseRoleFromName($userOID, $roleName){
    $user = \PPHP\model\modules\System\Users\User::getProxy($userOID);
    $role = $this->essenceManager->recoverRoleFromName($roleName);
    return $this->essenceManager->removeLinkageUserRole($user, $role);
  }

  /**
   * Метод возвращает массив ролей, делегированных пользователю.
   * @param integer $userOID Идентификатор пользователя.
   * @return Role[]
   */
  public function getRolesUser($userOID=null){
    $user = (is_null($userOID))? new \PPHP\model\modules\System\Users\DefaultUser : \PPHP\model\modules\System\Users\User::getProxy($userOID);
    $roles = $this->essenceManager->getRolesUser($user);
    $result = [];
    foreach($roles as $v){
      $result[] = $v;
    }
    return $result;
  }

  /**
   * Метод добавляет новое право доступа.
   * @param string $module Имя модуля.
   * @param string $action Запрещаемый метод контроллера модуля.
   * @return integer Идентификатор добавленного права доступа.
   */
  public function addRule($module, $action){
    return $this->essenceManager->addRule($module, $action);
  }

  /**
   * Метод удаляет право доступа.
   * @param integer $ruleOID Идентификатор удаляемого права доступа.
   * @return boolean true - если право доступа успешно удалено.
   */
  public function deleteRule($ruleOID){
    return $this->essenceManager->deleteRule($this->essenceManager->recoverRule($ruleOID));
  }

  /**
   * Метод возвращает право доступа по его идентификатору.
   * @param integer $ruleOID Идентификатор права доступа.
   * @return boolean|Rule Право доступа или false - если заданного права не существует.
   */
  public function getRule($ruleOID){
    return $this->essenceManager->recoverRule($ruleOID);
  }

  /**
   * Метод возвращает право доступа по целевому методу контроллера модуля.
   * @param string $module Имя модуля.
   * @param string $action Имя запрещаемого метода контроллера модуля.
   * @return boolean|Rule Право доступа или false - если заданного права не существует.
   */
  public function getRuleFromPurpose($module, $action){
    return $this->essenceManager->recoverRuleFromPurpose($module, $action);
  }

  /**
   * Метод расширяет заданную роль правом доступа.
   * @param integer $roleOID Идентификатор расширяемой роли.
   * @param integer $ruleOID Идентификатор расширяющего права доступа.
   * @return boolean true - если расширение успешно произведено.
   */
  public function expandRole($roleOID, $ruleOID){
    $role = Role::getProxy($roleOID);
    $rule = Rule::getProxy($ruleOID);
    return $this->essenceManager->addLinkageRoleRule($role, $rule);
  }

  /**
   * Метод сужает роль, отнимая заданное право доступа.
   * @param integer $roleOID Идентификатор сужаемой роли.
   * @param integer $ruleOID Идентификатор отнимаемого права доступа.
   * @return boolean true - если сужение успешно произведено.
   */
  public function narrowRole($roleOID, $ruleOID){
    $role = Role::getProxy($roleOID);
    $rule = Rule::getProxy($ruleOID);
    return $this->essenceManager->removeLinkageRoleRule($role, $rule);
  }
}

Controller::getReflectionClass()->setMetadata('ParentModule', 'Users');