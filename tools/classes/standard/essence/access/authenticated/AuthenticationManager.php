<?php
namespace PPHP\tools\classes\standard\essence\access\authenticated;

use \PPHP\tools\patterns as patterns;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\classes\standard\storage\database as database;
use \PPHP\services as services;

/**
 * Менеджер аутентификации содержит механизмы восстановления аутентифицируемых сущностей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\essence\access\authenticated
 */
class AuthenticationManager implements patterns\singleton\Singleton{
  use patterns\singleton\TSingleton;

  /**
   * Интерфейс взаимодействия с БД.
   * @var database\DataMapper
   */
  protected $dataMapper;

  /**
   * Метод устанавливает Data Mapper данному классу для работы с СУБД.
   * @param database\DataMapper $dataMapper Интерфейс ORM.
   */
  public function setDataMapper(database\DataMapper $dataMapper){
    $this->dataMapper = $dataMapper;
  }

  /**
   * @return database\DataMapper
   */
  public function getDataMapper(){
    return $this->dataMapper;
  }

  /**
   * Метод устанавливает стандартные инструменты в качестве DataMapper.
   * @throws exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return AuthenticationManager Возвращает вызываемый объект для организации цепочек вызова.
   */
  public function useStandardConnectTools(){
    try{
      $this->dataMapper = services\database\DataMapperManager::getInstance()->getDataMapper();
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }

    return $this;
  }

  /**
   * Метод пытается аутентифицировать сущность по заданной ключевой паре. Если сущность аутентифицирована, она восстанвливает свое последнее состояние.
   * @param AuthenticatedEntity $entity Аутентифицируемая сущность.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   * @return boolean true - если аутентификация успешна и сущность восстановлена, иначе - false.
   */
  public function authenticate(AuthenticatedEntity &$entity){
    $OID = $entity->getOID();
    $password = $entity->getPassword();
    exceptions\InvalidArgumentException::verifyType($OID, 'i');
    exceptions\InvalidArgumentException::verifyType($password, 'S');
    try{
      $this->dataMapper->recoverFinding($entity, ['OID' => $OID, 'password' => $password]);

      return true;
    }
    catch(database\UncertaintyException $e){
      return false;
    }
    catch(exceptions\PDOException $e){
      services\log\LogManager::getInstance()->setMessage(services\log\Message::createError('Системная ошибка при обращении к базе данных.', $e));

      return false;
    }
  }
}
