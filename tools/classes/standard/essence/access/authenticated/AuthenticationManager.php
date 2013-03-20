<?php
namespace PPHP\tools\classes\standard\essence\access\authenticated;
use \PPHP\tools\patterns as patterns;
use \PPHP\tools\classes\standard\storage\database as database;
use \PPHP\tools\classes\standard\essence\access\authenticated as authenticated;

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
   * @return authenticated\AuthenticationManager Возвращает вызываемый объект для организации цепочек вызова.
   */
  public function useStandardConnectTools(){
    $this->dataMapper = database\DataMapperManager::getInstance()->getDataMapper();
    return $this;
  }

  /**
   * Метод пытается аутентифицировать сущность по заданной ключевой паре. Если сущность аутентифицирована, она восстанвливает свое последнее состояние.
   * @param authenticated\AuthenticatedEntity $entity Аутентифицируемая сущность.
   * @return boolean true - если аутентификация успешна и сущность восстановлена, иначе - false.
   */
  public function authenticate(AuthenticatedEntity &$entity){
    $OID = $entity->getOID();
    $password = $entity->getPassword();

    try{
      $this->dataMapper->recoverFinding($entity, ['OID' => $OID, 'password' => $password]);
      return true;
    }
    catch(database\UncertaintyException $e){
      return false;
    }
  }

  /**
   * Метод позволяет зарегистрировать новую аутентифицируемую сущность в системе.
   *
   * @param AuthenticatedEntity $entity Регистрируемая сущность.
   *
   * @throws database\QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   */
  public function register(AuthenticatedEntity &$entity){
    $this->dataMapper->insert($entity);
  }
}
