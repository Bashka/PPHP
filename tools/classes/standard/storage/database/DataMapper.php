<?php
namespace PPHP\tools\classes\standard\storage\database;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\associations\LongAssociation;
use \PPHP\tools\patterns\database\LongObject;
use \PPHP\tools\classes\standard\storage\database\ORM as ORM;
use PPHP\tools\patterns\memento\Memento;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Класс позволяет сохранять, восстанавливать, обновлять и удалять состояния персистентных объектов в реляционых базах данных по средствам SQL запросов к ним.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database
 */
class DataMapper{
  /**
   * Имя класса, объекты которого ассоциированы с данным объектом множественной связью.
   */
  const ORM_ASSOC_CLASS = 'ORM\Assoc';

  /**
   * Имя свойства, которым ассоциированные объекты ссылаются на данный объект.
   */
  const ORM_ASSOC_FK = 'ORM\FK';

  /**
   * Маркер, определяющий композиционную множественную ассоциацию. Свойства множественных ассоциаций, помеченные данным маркером, определяют классы, объекты которых должны быть удалены при удалении агрегата.
   */
  const ORM_COMPOSITION = 'ORM\Composition';

  /**
   * Маркерная аннотация, определяющая ассоциативное свойство, которое выполняет полную инициализацию связанных объектов.
   */
  const ORM_FULL = 'ORM\Full';

  /**
   * Соединение с базой данных через PDO интерфейс.
   * @var PDO;
   */
  protected $PDO;

  /**
   * Метод восстанавливает объект на основании массива свойств.
   * Свойства класса объекта, аннотированные по средствам метаданных ORM_ASSOC_CLASS и ORM_ASSOC_FK восстанавливаются в виде LongAssociation как множества объектов, ссылающихся на данных, восстанавливаемый объект. Метод восстановления ассоциации "ленивый".
   * @param LongObject $object Восстанавливаемый объект.
   * @param array $data Массив свойств.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   */
  protected function setStateObject(LongObject &$object, array $data){
    // Определение идентификатора объекта
    if(!$object->isOID()){
      $object->setOID($data['OID']);
    }

    // Преобразование объектных ссылок в Proxy
    foreach($data as $name => &$value){
      if(is_string($value) && LongObject::isReestablish($value)){
        $value = LongObject::reestablish($value);
        // Полное восстановление связи
        if($object->getReflectionProperty($name)->isMetadataExists(self::ORM_FULL)){
          $this->recover($value);
        }
      }
    }

    // Восстановление множественных ассоциаций
    $reflectionProperties = $object->getAllReflectionProperties();
    foreach($reflectionProperties as $property){
      // Работа только со свойствами, имеющими соответствующие аннотации
      if($property->isMetadataExists(self::ORM_ASSOC_CLASS) && $property->isMetadataExists(self::ORM_ASSOC_FK)){
        $reflectionAssocCLass = $property->getMetadata(self::ORM_ASSOC_CLASS);
        $reflectionAssocCLass = $reflectionAssocCLass::getReflectionClass();
        try{
          $assoc = new LongAssociation(ORM\Select::metamorphoseAssociation($reflectionAssocCLass, [[$property->getMetadata(self::ORM_ASSOC_FK), '=', $object->interpretation()]]), $reflectionAssocCLass);
          // Полное восстановление связи
          if($property->isMetadataExists(self::ORM_FULL)){
            $this->recoverAssoc($assoc);
          }
          $data[$property->getName()] = $assoc;
        }
        catch(exceptions\NotFoundDataException $e){
          throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
        }
      }
    }

    // Выброс исключений не предполагается
    $object->restoreFromMemento(new Memento($object, $data));
  }

  /**
   * Метод позволяет установить интерфейс доступа к базе данных.
   * @param PDO $PDO Средство доступа к базе данных.
   */
  public function setPDO(PDO $PDO){
    $this->PDO = $PDO;
  }

  /**
   * Метод добавляет объект в базу данных одновременно устанавливая для него текущий идентификатор.
   * @param LongObject $object Добавляемый объект.
   * @param integer $newOID Идентификатор нового объекта.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  public function insert(LongObject &$object, $newOID){
    try{
      $inserts = ORM\Insert::metamorphose($object, $newOID);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }

    try{
      $this->PDO->multiQuery($inserts);
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }
    // Выброс исключения exceptions\InvalidArgumentException не предполагается

    // Выброс исключений не предполагается
    $object->setOID($newOID);
  }

  /**
   * Метод обновляет данные о состоянии объекта к базе данных.
   * @param LongObject $object Обновляемый объект.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   */
  public function update(LongObject $object){
    try{
      $updates = ORM\Update::metamorphose($object);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    // Выброс исключения exceptions\InvalidArgumentException не предполагается

    try{
      $this->PDO->multiQuery($updates);
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }
    // Выброс исключения exceptions\InvalidArgumentException не предполагается
  }

  /**
   * Метод удаляет данные из базы данных.
   * Если одно или несколько свойств объекта, определяющих множественную ассоциацию аннотированы композитным маркером, то объекты, ассоциированны с данным классом, будут так же удалены.
   * @param LongObject $object Удаляемый объект.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   */
  public function delete(LongObject $object){
    // Формирование транзакции на удаление сущности
    try{
      $updates = ORM\Delete::metamorphose($object);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    // Выброс исключения exceptions\InvalidArgumentException не предполагается

    // Удаление композита
    $reflectionProperties = $object->getAllReflectionProperties();
    foreach($reflectionProperties as $property){
      // Работа только со свойствами, имеющими соответствующие аннотации
      if($property->isMetadataExists(self::ORM_ASSOC_CLASS) && $property->isMetadataExists(self::ORM_ASSOC_FK) && $property->isMetadataExists(self::ORM_COMPOSITION)){
        $reflectionAssocCLass = $property->getMetadata(self::ORM_ASSOC_CLASS);
        $reflectionAssocCLass = $reflectionAssocCLass::getReflectionClass();
        $components = $this->recoverGroupFinding($reflectionAssocCLass, [[$property->getMetadata(self::ORM_ASSOC_FK), '=', $object]]);
        foreach($components as $component){
          $this->delete($component);
        }
      }
    }

    // Удаление сущности
    try{
      $this->PDO->multiQuery($updates);
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }
    // Выброс исключения exceptions\InvalidArgumentException не предполагается
  }

  /**
   * Метод восстанавливает состояние объекта из базы данных.
   * @param LongObject $object Востанавливаемый объект.
   * @throws UncertaintyException Выбрасывается в случае, если результатом запроса является множество записей или ни одной записи.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   */
  public function recover(LongObject &$object){
    try{
      $select = ORM\Select::metamorphose($object);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    //Дальнейший выброс исключений не предполагается

    try{
      $queryResult = $this->PDO->query($select->interpretation());
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }

    if($queryResult->rowCount() != 1){
      throw new UncertaintyException('Запрашиваемое состояние объекта ['.get_class($object).':'.$object->getOID().'] не найдено в базе данных или результат неоднозначен. Восстановление невозможно.');
    }

    $queryResult = $queryResult->fetch(\PDO::FETCH_ASSOC);

    // Восстановление объекта
    $this->setStateObject($object, $queryResult);
  }

  /**
   * Метод восстанавливает состояние объекта из базы данных производя поиск состояния на основании массива требуемых значений.
   * @param LongObject $object Восстанавливаемый, не идентифицированный объект.
   * @param array $conditions Ассоциативный массив, определяющий условие отбора. Массив имеет следующую структуру: [[имяСвойства, оператор, значение], ...].
   * @throws UncertaintyException Выбрасывается в случае, если результатом запроса является множество записей или ни одной записи.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   */
  public function recoverFinding(LongObject &$object, array $conditions){
    try{
      $select = ORM\Select::metamorphoseAssociation($object->getReflectionClass(), $conditions);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    //Дальнейший выброс исключений не предполагается

    try{
      $queryResult = $this->PDO->query($select->interpretation());
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }

    if($queryResult->rowCount() != 1){
      throw new UncertaintyException('Запрашиваемое состояние объекта ['.get_class($object).':'.$object->getOID().'] не найдено в базе данных или результат неоднозначен. Восстановление невозможно.');
    }

    $queryResult = $queryResult->fetch(\PDO::FETCH_ASSOC);

    // Восстановление объекта
    $this->setStateObject($object, $queryResult);
  }

  /**
   * Метод восстанавливает множество объектов согласно массиву требований к значениям их свойств.
   * @param ReflectionClass $reflectionClass Восстанавливаемый класс объектов.
   * @param array $conditions Массив требований к значениям свойств.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @throws exceptions\StructureException Выбрасывается в случае передачи недопустимого для работы объекта.
   * @return LongObject[] Ассоциативный массив восстановленных согласно условию отбора объектов. Ключами массива являются идентификаторы объектов. Пустой массив если объектов не найдено.
   */
  public function recoverGroupFinding(ReflectionClass $reflectionClass, array $conditions){
    try{
      $select = ORM\Select::metamorphoseAssociation($reflectionClass, $conditions);
    }
    catch(exceptions\NotFoundDataException $e){
      throw new exceptions\StructureException('Недопустимый персистентный объект.', 1, $e);
    }
    //Дальнейший выброс исключений не предполагается

    try{
      $queryResult = $this->PDO->query($select->interpretation());
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }

    // Формирование массива объектов
    $result = [];
    $className = $reflectionClass->getName();
    while($row = $queryResult->fetch(PDO::FETCH_ASSOC)){
      $object = $className::getProxy($row['OID']);
      $this->setStateObject($object, $row);
      $result[$row['OID']] = $object;
    }

    return $result;
  }

  /**
   * Метод восстанавливает множественную ассоциацию.
   * @param LongAssociation $assoc Восстанавливаемая ассоциация.
   * @throws exceptions\PDOException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  public function recoverAssoc(LongAssociation &$assoc){
    try{
      $queryResult = $this->PDO->query($assoc->getSelectQuery()->interpretation());
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }

    // Восстановление ассоциации
    $assoc->removeAll($assoc);
    $className = $assoc->getAssocClass()->getName();
    while($row = $queryResult->fetch(PDO::FETCH_ASSOC)){
      $object = $className::getProxy($row['OID']);
      $this->setStateObject($object, $row);
      $assoc->attach($object);
    }
  }
}