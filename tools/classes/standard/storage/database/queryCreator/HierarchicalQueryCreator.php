<?php
namespace PPHP\tools\classes\standard\storage\database\queryCreator;

/**
 * Конструктор запросов, позволяющий создавать SQL запрос для работы с иерархиями данных.
 */
class HierarchicalQueryCreator extends AssociationQueryCreator{
  /**
   * Метод создает SQL запрос, позволяющий переопределить иерархическую зависимость дочерних элементов узла до родительского элемента узла.
   * После выполнения запроса все дочерние элементы заданного узла будут дочерними элементами того узла, который является родительским по отношению к заданному.
   * @param \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object Заданный узел.
   * @return \PPHP\tools\patterns\database\query\Update Целевой SQL запрос.
   * @throws \PPHP\tools\patterns\database\identification\IncorrectOIDException Выбрасывается в случае, если заданный узел не имеет идентификатора.
   */
  public function createUpdateChildQuery(\PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object){
    if(!$object->isOID()){
      throw new \PPHP\tools\patterns\database\identification\IncorrectOIDException();
    }
    $reflectionMainClass = $object->getReflectionClass();
    $update = new \PPHP\tools\patterns\database\query\Update(new \PPHP\tools\patterns\database\query\Table($reflectionMainClass->getMetadata('NameTable')));
    $parent = $object->getHierarchicalParent();
    $parentField = new \PPHP\tools\patterns\database\query\Field($object->getReflectionProperty('hierarchicalParent')->getMetadata('NameFieldTable'));
    $update->addData($parentField, ($parent)? IndividualQueryCreator::serializeIndividualObject($parent) : 'NULL');
    $update->insertWhere(new \PPHP\tools\patterns\database\query\Where(new \PPHP\tools\patterns\database\query\LogicOperation($parentField, '=', IndividualQueryCreator::serializeIndividualObject($object))));
    return $update;
  }
}
