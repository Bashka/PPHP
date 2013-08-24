<?php
namespace PPHP\services\database;

use PPHP\tools\classes\standard\baseType\exceptions\PDOException;
use PPHP\tools\classes\standard\storage\database\DataMapper;
use PPHP\tools\classes\standard\storage\database\PDO;
use PPHP\tools\patterns\singleton as singleton;

/**
 * Класс позволяет соединиться с БД через кольцо объектно-реляционного преобразования.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\database
 */
class DataMapperManager implements singleton\Singleton{
  use singleton\TSingleton;

  /**
   * Соединение с БД.
   * @var PDO
   */
  protected $PDO;

  /**
   * Текущее соединение с БД через кольцо объектно-реляционного преобразования.
   * @var DataMapper
   */
  protected $dataMapper;

  /**
   * @throws PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   */
  private function __construct(){
    try{
      $this->PDO = ConnectionManager::getInstance()->getPDO();
    }
    catch(PDOException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает новое соединение с БД через кольцо объектно-реляционного преобразования.
   * @return DataMapper Соединение с БД.
   */
  public function getNewDataMapper(){
    $dataMapper = new DataMapper;
    $dataMapper->setPDO($this->PDO);

    return $dataMapper;
  }

  /**
   * Метод возвращает существующее соединение с БД через кольцо объектно-реляционного преобразования.
   * @return DataMapper Соединение с БД.
   */
  public function getDataMapper(){
    if(empty($this->dataMapper)){
      $this->dataMapper = $this->getNewDataMapper();
    }

    return $this->dataMapper;
  }
}
