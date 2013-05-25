<?php
namespace PPHP\tools\classes\standard\essence\structures\hierarchical;
use \PPHP\tools\patterns\database as database;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет сущности, имеющие неограниченные по вертикали и горизонтали иерархические связи между другими сужностями.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\essence\structures\hierarchical
 */
abstract class HierarchicalEntity extends database\LongObject{
  /**
   * Идентификатор родительской сущности. null если сущность является корнем иерархии и целочисленный тип если объект еще не был восстановлен.
   * @var HierarchicalEntity|null|integer
   */
  protected $hierarchicalParent;

  /**
   * Множество дочерних объектов.
   * Данное свойтсво должно быть аннотированно для множественной, ассоциативной выборки дочерних элементов с использованием ключей AssocClass и KeyAssocTable.
   * @var database\associations\LongAssociation
   */
  protected $hierarchicalChild;

  public function getHierarchicalParent(){
    return $this->hierarchicalParent;
  }


  /**
   * @param integer $parentOID
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function setHierarchicalParent($parentOID){
    exceptions\InvalidArgumentException::verifyType($parentOID, 'i');

    $this->hierarchicalParent = $parentOID;
  }

  /**
   * @return database\associations\LongAssociation
   */
  public function &getHierarchicalChild(){
    return $this->hierarchicalChild;
  }
}

HierarchicalEntity::getReflectionClass()->setMetadata('NameTable', 'HierarchicalEntity');
HierarchicalEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');

HierarchicalEntity::getReflectionProperty('hierarchicalParent')->setMetadata('NameFieldTable', 'hierarchicalParent');