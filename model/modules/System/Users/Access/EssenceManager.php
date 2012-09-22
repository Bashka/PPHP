<?php
namespace PPHP\model\modules\System\Users\Access;

/**
 * Менеджер предоставляет механизмы управления ролями и правами доступа.
 */
class EssenceManager extends \PPHP\tools\classes\standard\storage\database\EssenceManager{
  // User
  /**
   * Метод возвращает множесво ролей (proxy) данного пользователя.
   * @param \PPHP\model\modules\System\Users\User $user Пользователь (proxy), для которого необходимо определить его роли.
   * @return \SplObjectStorage Множество объектов класса Role (proxy).
   */
  public function getRolesUser(\PPHP\model\modules\System\Users\User $user){
    $this->setDataMapper();
    $roles = new \SplObjectStorage;
    if($user instanceof \PPHP\model\modules\System\Users\DefaultUser){
      $role = new Role;
      try{
        $this->dataMapper->recoverFinding($role, [Role::getReflectionProperty('name')->getMetadata('NameFieldTable') => 'Default user role']);
      }
      catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
        return $roles;
      }
      $roles->attach($role);
    }
    else{
      $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager('\PPHP\model\modules\System\Users\Access\LinkageUserRole');
      $linksRole = $this->dataMapper->recoverGroupFinding(\PPHP\model\modules\System\Users\Access\LinkageUserRole::getReflectionClass(), [$metadataManager->getReflectionProperty('user')->getMetadata('NameFieldTable') => $user]);
      foreach($linksRole as $linkRole){
        $role = $linkRole->getRole();
        $roles->attach($role);
      }
    }
    $roles->rewind();
    return $roles;
  }

  /**
   * Метод делегирует роль пользователю.
   * @param \PPHP\model\modules\System\Users\User $user Целевой пользователь (proxy).
   * @param Role $role Делегируемая роль (proxy).
   * @return boolean true - если роль добавлена удачно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если на момент вызова метода пользователю уже была делегированная данная роль.
   */
  public function addLinkageUserRole(\PPHP\model\modules\System\Users\User $user, Role $role){
    $activeRoles = $this->getRolesUser($user);
    // Проверка на дублирование роли.
    foreach($activeRoles as $activeRole){
      if($activeRole->getOID() == $role->getOID()){
        throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Данная роль уже была делегированная пользователю.');
      }
    }
    $linkUserRole = new LinkageUserRole;
    $linkUserRole->setRole($role);
    $linkUserRole->setUser($user);
    $this->addEssence($linkUserRole);
    return true;
  }

  /**
   * Метод отзывает роль у пользователя.
   * @param \PPHP\model\modules\System\Users\User $user Целевой пользователь (proxy).
   * @param Role $role Отзываемая роль (proxy).
   * @return boolean true - если роль отозвана удачно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если на момент вызова метода указанная роль не была делегированная пользователю.
   */
  public function removeLinkageUserRole(\PPHP\model\modules\System\Users\User $user, Role $role){
    $linkUserRole = new LinkageUserRole;
    try{
      $this->setDataMapper();
      $this->dataMapper->recoverFinding($linkUserRole, [LinkageUserRole::getReflectionProperty('user')->getMetadata('NameFieldTable') => $user, $linkUserRole->getReflectionProperty('role')->getMetadata('NameFieldTable') => $role]);
      $this->deleteEssence($linkUserRole);
      return true;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $e){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Данная роль не была делегированная пользователю.');
    }
  }

  /**
   * Метод возвращает множество прав, определенных ролями данного пользователя.
   * @param \PPHP\model\modules\System\Users\User $user Пользователь, для которого необходимо определить его права (proxy).
   * @return \SplObjectStorage Множество объектов класса Rule (proxy).
   */
  public function getRulesUser(\PPHP\model\modules\System\Users\User $user){
    $roles = $this->getRolesUser($user);
    $rules = new \SplObjectStorage;
    foreach($roles as $role){
      $rulesRole = $this->getRulesRole($this->recoverRole($role->getOID()));
      foreach($rulesRole as $rule){
        $rules->attach($rule);
      }
    }
    $rules->rewind();
    return $rules;
  }

  // Role
  /**
   * Метод добавляет новую роль.
   * @param string $name Имя добавляемой роли.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если роль с заданным именем уже существует в системе.
   * @return integer OID добавленной роли.
   */
  public function addRole($name){
    if($this->recoverRoleFromName($name)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Роль с заданным именем существует.');
    }
    $role = new Role;
    $role->setName($name);
    $this->addEssence($role);
    return $role->getOID();
  }

  /**
   * Метод восстанавливает роль по данным ее идентификатора.
   * @param integer $OID Идентификатор восстанавливаемой роли.
   * @return Role|boolean Восстановленный экземпляр класса Role или false - если роли с заданным идентификатором нет в системе.
   */
  public function recoverRole($OID){
    try{
      return $this->recoverEssence(Role::getProxy($OID));
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }

  /**
   * Метод восстанавливает роль по ее имени.
   * @param string $name Имя восстанавливаемой роли.
   * @return Role|boolean Восстановленная роль или false - если данной роли не существует.
   */
  public function recoverRoleFromName($name){
    $this->setDataMapper();
    $role = new Role;
    try{
      $this->dataMapper->recoverFinding($role, ['name' => $name]);
      return $role;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }

  /**
   * Метод возвращает множество экземпляров класса Rule (proxy), расширяющих заданную роль.
   * @param Role $role Роль, права которой необходимо определить.
   * @return \SplObjectStorage Множество прав доступа, расширяющих данную роль (proxy).
   */
  public function getRulesRole(Role $role){
    $rulesAssoc = $role->getRules();
    $this->dataMapper->recoverAssoc($rulesAssoc);
    $rules = new \SplObjectStorage;
    foreach($rulesAssoc as $ruleLink){
      $rule = $ruleLink->getRule();
      $rules->attach($rule);
    }
    $rules->rewind();
    return $rules;
  }

  /**
   * Метод удаляет роль и все связи ее с правами доступа.
   * @param Role $role Удаляемая роль.
   * @return boolean true - если роль успешно удалена.
   */
  public function deleteRole(Role $role){
    $rulesAssoc = $role->getRules();
    $this->dataMapper->recoverAssoc($rulesAssoc);
    foreach($rulesAssoc as $ruleLink){
      $this->deleteEssence($ruleLink);
    }
    $this->deleteEssence($role);
    return true;
  }

  /**
   * Метод переименовывает заданную роль.
   * @param Role $role Переименовываемая роль.
   * @param string $newName Новое имя роли.
   * @return boolean true - если роль удачно переименована.
   */
  public function renameRole(Role $role, $newName){
    $role->setName($newName);
    $this->updateEssence($role);
    return true;
  }

  /**
   * Метод расширяет роль правом доступа.
   * @param Role $role Целевая роль (proxy).
   * @param Rule $rule Расширяющее право (proxy).
   * @return boolean true - если связь успешно установленна.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если на момент вызова метода роль уже включало данное правило.
   */
  public function addLinkageRoleRule(Role $role, Rule $rule){
    $this->setDataMapper();
    $this->dataMapper->recover($role);
    $activeRules = $this->getRulesRole($role);
    // Проверка на дублирование права.
    foreach($activeRules as $activeRule){
      if($activeRule->getOID() == $rule->getOID()){
        throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Данные права уже делегируются ролью.');
      }
    }
    $linkRoleRule = new LinkageRoleRule();
    $linkRoleRule->setRole($role);
    $linkRoleRule->setRule($rule);
    $this->addEssence($linkRoleRule);
    return true;
  }

  /**
   * Метод отзывает право доступа у роли.
   * @param Role $role Целевая роль (proxy).
   * @param Rule $rule Отзываемое право доступа (proxy).
   * @return boolean true - если связь успешно удалена.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если на момент вызова метода указанное право доступа не расширяло роль.
   */
  public function removeLinkageRoleRule(Role $role, Rule $rule){
    $this->setDataMapper();
    $linkRoleRule = new LinkageRoleRule();
    try{
      $this->dataMapper->recoverFinding($linkRoleRule, [LinkageRoleRule::getReflectionProperty('rule')->getMetadata('NameFieldTable') => $rule, $linkRoleRule->getReflectionProperty('role')->getMetadata('NameFieldTable') => $role]);
      $this->dataMapper->delete($linkRoleRule);
      return true;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $e){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Данное право не расширяет роль.');
    }
  }

  // Rule
  /**
   * Метод восстанавливает право доступа по данным его идентификатора.
   * @param integer $OID Идентификатор восстанавливаемого права доступа.
   * @return Rule|boolean Восстановленный экземпляр класса Rule или false - если права доступа с заданным идентификатором не существует в системе.
   */
  public function recoverRule($OID){
    try{
      return $this->recoverEssence(Rule::getProxy($OID));
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }

  /**
   * Метод восстанавливает право доступа по данным ее целевого метода контроллера.
   * @param string $module Целевой модуль.
   * @param string $active Целевой метод контроллера модуля.
   * @return boolean|\PPHP\model\modules\System\Users\Access\Rule Восстановленное право доступа или false - если данного права не существует.
   */
  public function recoverRuleFromPurpose($module, $active){
    $this->setDataMapper();
    $rule = new Rule;
    try{
      $this->dataMapper->recoverFinding($rule, ['module' => $module, 'action' => $active]);
      return $rule;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $exc){
      return false;
    }
  }

  /**
   * Метод добавляет право доступа в систему.
   * @param string $module Имя модуля.
   * @param string $active Запрещаемый метод контроллера данного модуля.
   * @return integer Идентификатор добавленного права доступа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если право доступа с заданным целевым методом уже существует в системе.
   */
  public function addRule($module, $active){
    if($this->recoverRuleFromPurpose($module, $active)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Право доступа с заданным целевым методом уже существует.');
    }
    $rule = new Rule;
    $rule->setModule($module);
    $rule->setAction($active);
    $this->addEssence($rule);
    return $rule->getOID();
  }

  /**
   * Метод удаляет заданное право доступа из системы и все связи с ним.
   * @param \PPHP\model\modules\System\Users\Access\Rule $rule Удаляемое право доступа.
   * @return boolean true - если право доступа успешно удалено.
   */
  public function deleteRule(Rule $rule){
    $this->setDataMapper();
    $links = $this->dataMapper->recoverGroupFinding(LinkageRoleRule::getReflectionClass(), ['rule' => $rule]);
    foreach($links as $link){
      $this->dataMapper->delete($link);
    }
    $this->deleteEssence($rule);
    return true;
  }
}
