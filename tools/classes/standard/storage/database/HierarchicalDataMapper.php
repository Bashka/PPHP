<?php
namespace PPHP\tools\classes\standard\storage\database;


class HierarchicalDataMapper extends DataMapper{
  /**
   * Конструктор запросов.
   * @var \PPHP\tools\classes\standard\storage\database\queryCreator\HierarchicalQueryCreator
   */
  protected $queryCreator;

  /**
   * Метод удаляет заданный узел в иерархии, перемещая ветвь вверх.
   * @param \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object Удаляемый узел.
   */
  public function deleteNode(\PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object){
    $transaction = [$this->queryCreator->createUpdateChildQuery($object)];
    $transaction[] = $this->queryCreator->createDeleteQuery($object);
    $this->transactionQuery($transaction);
  }

  /**
   * Метод удаляет заданный узел в иерархии со всей дочерней ветвью.
   * @param \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object Узел, являющийся корневым в удаляемой ветви.
   */
  public function deleteBranch(\PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $object){
    $this->PDO->beginTransaction();
    $child = $object->getHierarchicalChild();
    $this->delete($object);
    if($child->count() > 0){
      foreach($child as $v){
        $this->deleteBranch($v);
      }
    }
    $this->PDO->rollBack();
  }
}


