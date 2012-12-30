<?php
namespace PPHP\tools\classes\standard\storage\database;

/**
 * Класс позволяет взаимодействовать с реляционной базой данных через объектно-ориентированный интерфейс.
 */
class DataMapper{
  /**
   * Конструктор запросов.
   * @var \PPHP\tools\classes\standard\storage\database\queryCreator\AssociationQueryCreator
   */
  protected $queryCreator;

  /**
   * Соединение с базой данных через PDO интерфейс.
   * @var PDO;
   */
  protected $PDO;

  /**
   * Метод пытается восстановить объект из единичной ассоциации.
   * @static
   * @param mixed $data Восстанавливаемые данные.
   * @return \PPHP\tools\patterns\database\LongObject|mixed Фиктивный объект, полученый после восстановления, или значение аргумента, если эти данные не подлежат восстановлению.
   */
  static protected function unserializeObject($data){
    $elementsLink = [];
    if(is_string($data) && preg_match('/^\$([A-Z\/a-z]+):([0-9]+)$/', $data, $elementsLink)){
      $className = str_replace('/', '\\', $elementsLink[1]);
      return $className::getProxy($elementsLink[2]);
    }
    return $data;
  }

  /**
   * Метод позволяет установить конструктор запросов для преобразователя данных.
   * @param \PPHP\tools\classes\standard\storage\database\queryCreator\QueryCreator $queryCreator
   */
  public function setQueryCreator(\PPHP\tools\classes\standard\storage\database\queryCreator\QueryCreator $queryCreator){
    $this->queryCreator = $queryCreator;
  }

  /**
   * Метод позволяет установить интерфейс доступа к базе данных для преобразователя данных.
   * @param PDO $PDO
   */
  public function setPDO(PDO $PDO){
    $this->PDO = $PDO;
  }

  /**
   * Метод выполняет массив запросов к БД в рамках транзакции с откатом изменений в случае ошибки.
   * @param array $queries Массив запросов
   * @throws QueryException Выбрасывается в случае возникновения ошибки в запросе.
   */
  protected function transactionQuery(array $queries){
    foreach($queries as &$query){
      $query = $query->interpretation();
    }
    $this->PDO->multiQuery($queries);
  }

  /**
   * Метод восстанавливает объект на основании массива свойств.
   * @param \PPHP\tools\patterns\database\LongObject $object Восстанавливаемый объект.
   * @param array $data Массив свойств.
   */
  protected function recoverObject(\PPHP\tools\patterns\database\LongObject &$object, array $data){
    $data = array_map([$this, 'unserializeObject'], $data);
    $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager(get_class($object));
    $assocReflections = $metadataManager->getAllReflectionPropertiesWithMetadata('AssocClass');
    if(!$object->isOID()){
      $object->setOID($data['OID']); // Для успешного создания ассоциативных связей необходимо предварительно востановить OID объекта, если он не был восстановлен
    }
    foreach($assocReflections as $reflectProperty){
      $reflectAssocClass = \PPHP\tools\patterns\metadata\MetadataManager::getReflectionClassClass($reflectProperty->getMetadata('AssocClass'));
      $longAssoc = new \PPHP\tools\patterns\database\associations\LongAssociation($this->queryCreator->createAssociationSelectQuery($object, $reflectProperty), $reflectAssocClass);
      $data[$reflectProperty->getName()] = $longAssoc;
    }
    $object->restoreFromMemento(new \PPHP\tools\patterns\memento\Memento($object, $data));
  }

  /**
   * Метод востанавливает объект из БД на основании SELECT запроса.
   * @param \PPHP\tools\patterns\database\LongObject $object Востанавливаемый объект.
   * @param \PPHP\tools\patterns\database\query\Select $query Запрос - основание.
   * @throws UncertaintyException Выбрасывается в случае, если результатом запроса является множество записей или ни одной записи.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  protected function recoverObjectFromQuery(\PPHP\tools\patterns\database\LongObject &$object, \PPHP\tools\patterns\database\query\Select $query){
    $queryResult = $this->PDO->query($query->interpretation());
    if($queryResult->rowCount() != 1){
      throw new UncertaintyException();
    }
    $data = $queryResult->fetch(\PDO::FETCH_ASSOC);
    $this->recoverObject($object, $data);
  }

  /**
   * Метод востанавливает множество объектов из БД на основании SELECT запроса.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass Отражение класса, объекты которого восстанавливаются.
   * @param \PPHP\tools\patterns\database\query\Select $query Запрос - основание.
   * @return \SplObjectStorage Множество восстановленных объектов.
   */
  protected function recoverObjectsFromQuery(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass, \PPHP\tools\patterns\database\query\Select $query){
    $queryResult = $this->PDO->query($query->interpretation());
    $objects = new \SplObjectStorage();
    $className = $reflectionClass->getName();
    while($row = $queryResult->fetch(PDO::FETCH_ASSOC)){
      $object = $className::getProxy($row['OID']);
      $this->recoverObject($object, $row);
      $objects->attach($object);
    }
    return $objects;
  }

  /**
   * Метод добавляет объект в базу данных.
   * @param \PPHP\tools\patterns\database\LongObject $object Добавляемый объект.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @return void
   */
  public function insert(\PPHP\tools\patterns\database\LongObject &$object){
    $this->transactionQuery($this->queryCreator->createInsertQuery($object));
  }

  /**
   * Метод обновляет данные в базе данных согласно данному объекту.
   * @param \PPHP\tools\patterns\database\LongObject $object Обновляемый объект.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @return void
   */
  public function update(\PPHP\tools\patterns\database\LongObject $object){
    $this->transactionQuery($this->queryCreator->createUpdateQuery($object));
  }

  /**
   * Метод удаляет данные из базы данных.
   * @param \PPHP\tools\patterns\database\LongObject $object Удаляемый объект.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @return void
   */
  public function delete(\PPHP\tools\patterns\database\LongObject $object){
    $this->transactionQuery($this->queryCreator->createDeleteQuery($object));
  }

  /**
   * Метод восстанавливает состояние объекта из базы данных.
   * @param \PPHP\tools\patterns\database\LongObject $object Востанавливаемый объект.
   * @throws UncertaintyException Выбрасывается в случае, если результатом запроса является множество записей или ни одной записи.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  public function recover(\PPHP\tools\patterns\database\LongObject &$object){
    $this->recoverObjectFromQuery($object, $this->queryCreator->createSelectQuery($object));
  }

  /**
   * Метод восстанавливает состояние объекта из базы данных производя поиск состояния на основании массива требуемых значений.
   * @param \PPHP\tools\patterns\database\LongObject $object Восстанавливаемый объект.
   * @param array $requiredProperties Множество, определяющее требования к состоянию.
   * @throws UncertaintyException Выбрасывается в случае, если результатом запроса является множество записей или ни одной записи.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  public function recoverFinding(\PPHP\tools\patterns\database\LongObject &$object, array $requiredProperties){
    $this->recoverObjectFromQuery($object, $this->queryCreator->createFindingQuery($object::getReflectionClass(), $requiredProperties));
  }

  /**
   * Метод восстанавливает множество объектов согласно массиву требований к значениям их свойств.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass Восстанавливаемый класс объектов.
   * @param array $requiredProperties Массив требований к значениям свойств.
   * @return \SplObjectStorage
   */
  public function recoverGroupFinding(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass, array $requiredProperties){
    $query = $this->queryCreator->createFindingQuery($reflectionClass, $requiredProperties);
    $keyField = new \PPHP\tools\patterns\database\query\Field($reflectionClass->getMetadata('KeyTable'));
    $keyField->setTable(new \PPHP\tools\patterns\database\query\Table($reflectionClass->getMetadata('NameTable')));
    $query->addAliasField(new \PPHP\tools\patterns\database\query\FieldAlias($keyField, 'OID'));
    return $this->recoverObjectsFromQuery($reflectionClass, $query);
  }

  /**
   * Метод восстанавливает множественную ассоциацию.
   * @param \PPHP\tools\patterns\database\associations\LongAssociation $assoc Восстанавливаемая ассоциация.
   * @throws QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  public function recoverAssoc(\PPHP\tools\patterns\database\associations\LongAssociation &$assoc){
    $assoc->removeAll($assoc);
    $assoc->addAll($this->recoverObjectsFromQuery($assoc->getAssocClass(), $assoc->getSelectQuery()));
  }
}