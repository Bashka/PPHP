<?php
namespace PPHP\model\modules\System;

class Installer extends \PPHP\model\classes\Installer{
  public function install(){
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/System/temp', 0777);

    \PPHP\services\database\ConnectionManager::getInstance()->getPDO()->multiQuery('
      CREATE TABLE `AuthenticatedEntity` (
      `OID` INT NOT NULL PRIMARY KEY,
      `password` VARCHAR(100) NOT NULL
      );

      CREATE TABLE `HierarchicalEntity` (
      `OID` INT NOT NULL PRIMARY KEY,
      `hierarchicalParent` VARCHAR(100) NULL
      );
    ', ';');
  }

  public function uninstall(){
    \PPHP\services\database\ConnectionManager::getInstance()->getPDO()->multiQuery('
      DROP TABLE `AuthenticatedEntity`;
      DROP TABLE `HierarchicalEntity`;
    ', ';');
  }

}
