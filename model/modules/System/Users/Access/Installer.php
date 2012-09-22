<?php
namespace PPHP\model\modules\System\Users\Access;

class Installer extends \PPHP\model\classes\Installer{
  public function install(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->multiQuery('
      CREATE TABLE `Access_Rules` (
      `OID` INT NOT NULL PRIMARY KEY,
      `module` VARCHAR(80) NOT NULL,
      `action` VARCHAR(80) NOT NULL
      );

      CREATE TABLE `Access_Roles` (
      `OID` INT NOT NULL PRIMARY KEY,
      `name` VARCHAR(80) NOT NULL UNIQUE
      );

      CREATE TABLE `Access_RoleRule` (
      `OID` INT NOT NULL PRIMARY KEY,
      `role` VARCHAR(100) NOT NULL,
      `rule` VARCHAR(100) NOT NULL
      );

      CREATE TABLE `Access_UserRole` (
      `OID` INT NOT NULL PRIMARY KEY,
      `user` VARCHAR(100) NOT NULL,
      `role` VARCHAR(100) NOT NULL
      );',
    ';');

    $dataMapper = \PPHP\services\database\DataMapperManager::getInstance()->getDataMapper();
    $role = new Role;
    $role->setName('Default user role');
    $dataMapper->insert($role);
    $role = new Role;
    $role->setName('User role');
    $dataMapper->insert($role);
    $role = new Role;
    $role->setName('Moderator role');
    $dataMapper->insert($role);
    $role = new Role;
    $role->setName('Administrator role');
    $dataMapper->insert($role);
  }

  public function uninstall(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->multiQuery('
      DROP TABLE `Access_Rules`;
      DROP TABLE `Access_Roles`;
      DROP TABLE `Access_RoleRule`;
      DROP TABLE `Access_UserRole`;',
    ';');
  }
}