<?php
namespace PPHP\tools\classes\standard\storage\database;

/**
 * Класс реализует функционал иерархического менеджера сущностей.
 * В классе реализованы классические методы удаления иерархических сущностей.
 */
class HierarchicalEssenceManager extends EssenceManager{
  /**
   * Метод устанавливает иерархический Data Mapper объекту или получает новый, если в качестве параметра ничего не передано.
   * Если на момент вызова метода Data Mapper уже был установлен и в качестве аргумента ничего не переданно, то метод игнорируется.
   * @param null|\PPHP\tools\classes\standard\storage\database\HierarchicalDataMapper $dataMapper
   * @return static Вызываемый объект.
   */
  public function setDataMapper($dataMapper = null){
    if($dataMapper){
      $this->dataMapper = $dataMapper;
    }
    elseif(empty($this->dataMapper)){
      $this->dataMapper = new \PPHP\tools\classes\standard\storage\database\HierarchicalDataMapper();
      $this->dataMapper->setPDO(\PPHP\services\database\ConnectionManager::getInstance()->getPDO());
      $this->dataMapper->setQueryCreator(new \PPHP\tools\classes\standard\storage\database\queryCreator\HierarchicalQueryCreator(\PPHP\services\database\identification\Autoincrement::getInstance()));
    }
    return $this;
  }

  /**
   * Метод удаляет сущность сдвигая дочерние вверх в иерархии.
   * @param \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $entity Удаляемая сущность.
   */
  public function removeNode(\PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $entity){
    $this->setDataMapper();
    $this->dataMapper->deleteNode($entity);
  }

  /**
   * Метод удаляет сущность и всю ветвь зависимостей.
   * @param \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $entity Удаляемая сущность.
   */
  public function removeBranch(\PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity $entity){
    $this->setDataMapper();
    $this->dataMapper->removeBranch($entity);
  }
}
