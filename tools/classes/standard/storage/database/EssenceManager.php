<?php
namespace PPHP\tools\classes\standard\storage\database;

/**
 * Класс определяет функционал стандартного менеджера сущностей.
 * В классе реализованы классические методы добавления, изменения и удаления любых сущностей.
 */
class EssenceManager implements \PPHP\tools\patterns\singleton\Singleton{
  use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Инструмент Data Mapper, используемый для взаимодействия с СУБД.
   * @var \PPHP\tools\classes\standard\storage\database\DataMapper
   */
  protected $dataMapper;

  /**
   * Метод устанавливает Data Mapper объекту или получает новый, если в качестве параметра ничего не передано.
   * Если на момент вызова метода Data Mapper уже был установлен и в качестве аргумента ничего не переданно, то метод игнорируется.
   * @param \PPHP\tools\classes\standard\storage\database\DataMapper|null $dataMapper
   * @return static Вызываемый объект.
   */
  public function setDataMapper($dataMapper = null){
    if($dataMapper){
      $this->dataMapper = $dataMapper;
    }
    elseif(empty($this->dataMapper)){
      $this->dataMapper = \PPHP\services\database\DataMapperManager::getInstance()->getDataMapper();
    }
    return $this;
  }

  // Add
  /**
   * Метод добавляет новую сущность в систему.
   * @param \PPHP\tools\patterns\database\LongObject $essence Добавляемая сущность.
   * @return integer Идентификатор добавленной сущности..
   */
  public function addEssence(\PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $this->dataMapper->insert($essence);
    return $essence->getOID();
  }

  // Remove
  /**
   * Метод удаляет сущность.
   * @param \PPHP\tools\patterns\database\LongObject $essence Удаляемая сущность (proxy).
   * @return boolean true - если сущность успешно удалена.
   */
  public function removeEssence(\PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $this->dataMapper->delete($essence);
    return true;
  }

  /**
   * Метод удаляет множественную ассоциацию.
   * @param \PPHP\tools\patterns\database\associations\LongAssociation $assoc Удаляемая ассоциация (proxy).
   * @return boolean true - если удаление успешно.
   */
  public function removeAssoc(\PPHP\tools\patterns\database\associations\LongAssociation $assoc){
    $this->setDataMapper();
    $this->dataMapper->recoverAssoc($assoc);
    foreach($assoc as $link){
      $this->removeEssence($link);
    }
    return true;
  }

  /**
   * Метод удаляет множественную ассоциацию и ассоциированные сущности.
   * @param \PPHP\tools\patterns\database\associations\LongAssociation $assoc Множественная ассоциация (proxy).
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionMethod[] $gettersEssence Множество отражений методов, ответственных за возврат компонентов из ассоциации.
   * @return boolean true - если удаление успешно.
   */
  public function compositeRemoval(\PPHP\tools\patterns\database\associations\LongAssociation $assoc, array $gettersEssence){
    $this->setDataMapper();
    $this->dataMapper->recoverAssoc($assoc);
    foreach($assoc as $link){
      foreach($gettersEssence as $getterEssence){
        $getterEssence = $getterEssence->getName();
        $this->removeEssence($link->$getterEssence());
      }
      $this->removeEssence($link);
    }
    return true;
  }

  /**
   * Метод удаляет не идентифицированную множественную ассоциацию.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass Отражение класса ассоциации.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key Отражение свойства ключа в классе ассоциации.
   * @param \PPHP\tools\patterns\database\LongObject $essence Связанная сущность.
   * @return boolean true - в случае успеха.
   */
  public function findingRemoveAssoc(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $links = $this->dataMapper->recoverGroupFinding($assocClass, [$key->getName() => $essence]);
    foreach($links as $link){
      $this->dataMapper->delete($link);
    }
    return true;
  }

  /**
   * Метод удаляет не идентифицированную множественную ассоциацию и ассоциированные сущности.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass Отражение класса ассоциации.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key Отражение свойства ключа в классе ассоциации.
   * @param \PPHP\tools\patterns\database\LongObject $essence Связанная сущность (proxy).
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionMethod[] $gettersEssence Отражение метода, ответственного за возврат требуемой сущности из ассоциации.
   * @return boolean true - в случае успеха.
   */
  public function findingCompositeRemoval(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence, array $gettersEssence){
    $this->setDataMapper();
    $assoc = $this->recoverFindAssoc($assocClass, $key, $essence);
    foreach($assoc as $link){
      foreach($gettersEssence as $getterEssence){
        $getterEssence = $getterEssence->getName();
        $this->removeEssence($link->$getterEssence());
      }
      $this->removeEssence($link);
    }
    return true;
  }

  // Update
  /**
   * Метод изменяет существующую сущность в соответствии с ее текущим состоянием.
   * @param \PPHP\tools\patterns\database\LongObject $essence Измененная сущность.
   * @return boolean true - если сущность успешно изменена.
   */
  public function updateEssence(\PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $this->dataMapper->update($essence);
    return true;
  }

  // Recover
  /**
   * Метод восстанавливает сущность.
   * @param \PPHP\tools\patterns\database\LongObject $essence Восстанавливаемая сущность (proxy).
   * @return \PPHP\tools\patterns\database\LongObject Восстановленная сущность.
   */
  public function recoverEssence(\PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $this->dataMapper->recover($essence);
    return $essence;
  }

  /**
   * Метод восстанавливает не идентифицированную сущность.
   * @param \PPHP\tools\patterns\database\LongObject $essence Восстанавливаемая сущность.
   * @param array $requiredProperties Известные свойства - идентификаторы.
   */
  public function findEssence(\PPHP\tools\patterns\database\LongObject &$essence, array $requiredProperties){
    $this->setDataMapper();
    $this->dataMapper->recoverFinding($essence, $requiredProperties);
  }

  /**
   * Метод восстанавливает множество не идентифицированных сущностей.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $essenceClass Отражение класса восстанавливаемых сущностей.
   * @param array $requiredProperties Известные свойства - идентификаторы.
   * @return \SplObjectStorage Множество восстановленных сущностей.
   */
  public function findEssences(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $essenceClass, array $requiredProperties){
    $this->setDataMapper();
    return $this->dataMapper->recoverGroupFinding($essenceClass, $requiredProperties);
  }

  /**
   * Метод восстанавливает множество сущностей, используя множественную ассоциацию.
   * @param \PPHP\tools\patterns\database\associations\LongAssociation $assoc Целевая ассоциация (proxy).
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence Отражение getter метода, ответственного за возврат требуемой сущности из ассоциации.
   * @return \SplObjectStorage Множество восстановленных сущностей (proxy).
   */
  public function recoverComponents(\PPHP\tools\patterns\database\associations\LongAssociation $assoc, \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence){
    $this->setDataMapper();
    $this->dataMapper->recoverAssoc($assoc);
    $assocEssences = new \SplObjectStorage();
    $getterEssence = $getterEssence->getName();
    foreach($assoc as $essence){
      $assocEssences->attach($essence->$getterEssence());
    }
    $assocEssences->rewind();
    return $assocEssences;
  }

  /**
   * Метод восстанавливает не идентифицированную множественную ассоциацию.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass Отражение класса ассоциации.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key Отражение свойства ключа в классе ассоциации.
   * @param \PPHP\tools\patterns\database\LongObject $essence Ключевая сущность (proxy).
   * @return \SplObjectStorage Восстановленная множественная ассоциация.
   */
  public function recoverFindAssoc(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence){
    $this->setDataMapper();
    $assoc = $this->dataMapper->recoverGroupFinding($assocClass, [$key->getName() => $essence]);
    $assoc->rewind();
    return $assoc;
  }

  /**
   * Метод восстанавливает множество сущностей, используя не идентифицированную множественную ассоциацию.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass Отражение класса ассоциации.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key Отражение свойства ключа в классе ассоциации.
   * @param \PPHP\tools\patterns\database\LongObject $essence Связанная сущность (proxy).
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence Отражение метода, ответственного за возврат требуемой сущности из ассоциации.
   * @return \SplObjectStorage Множество восстановленных сущностей.
   */
  public function recoverFindComponents(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence, \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence){
    $this->setDataMapper();
    $links = $this->recoverFindAssoc($assocClass, $key, $essence);
    $components = new \SplObjectStorage();
    $getterEssence = $getterEssence->getName();
    foreach($links as $link){
      $components->attach($link->$getterEssence());
    }
    $components->rewind();
    return $components;
  }
}
