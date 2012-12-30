<?php
namespace PPHP\services\database;

/**
 * Класс позволяет соединиться с БД через кольцо объектно-реляционного преобразования.
 */
class DataMapperManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Соединение с БД.
   * @var \PPHP\tools\classes\standard\storage\database\PDO
   */
  protected $PDO;
  /**
   * Преобразователь данных.
   * @var \PPHP\tools\classes\standard\storage\database\queryCreator\AssociationQueryCreator
   */
  protected $queryCreator;
  /**
   * Текущее соединение с БД через кольцо объектно-реляционного преобразования.
   * @var \PPHP\tools\classes\standard\storage\database\DataMapper
   */
  protected $dataMapper;

  private function __construct(){
    $this->PDO = ConnectionManager::getInstance()->getPDO();
    $this->queryCreator = new \PPHP\tools\classes\standard\storage\database\queryCreator\AssociationQueryCreator(\PPHP\services\database\identification\Autoincrement::getInstance());
  }

  /**
   * Метод возвращает новое соединение с БД через кольцо объектно-реляционного преобразования.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return \PPHP\tools\classes\standard\storage\database\DataMapper Соединение с БД.
   */
  public function getNewDataMapper(){
    $dataMapper = new \PPHP\tools\classes\standard\storage\database\DataMapper();
    $dataMapper->setPDO($this->PDO);
    $dataMapper->setQueryCreator($this->queryCreator);
    return $dataMapper;
  }

  /**
   * Метод возвращает существующее соединение с БД через кольцо объектно-реляционного преобразования.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return \PPHP\tools\classes\standard\storage\database\DataMapper Соединение с БД.
   */
  public function getDataMapper(){
    if(empty($this->dataMapper)){
      $this->dataMapper = $this->getNewDataMapper();
    }
    return $this->dataMapper;
  }
}
