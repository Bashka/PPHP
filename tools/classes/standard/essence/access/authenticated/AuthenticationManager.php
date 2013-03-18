<?php
namespace PPHP\tools\classes\standard\essence\access\authenticated;

/**
 * Менеджер аутентификации содержит механизмы восстановления аутентифицируемых сущностей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\essence\access\authenticated
 */
class AuthenticationManager implements \PPHP\tools\patterns\singleton\Singleton{
  use \PPHP\tools\patterns\singleton\TSingleton;
  /**
   * Интерфейс взаимодействия с БД.
   * @var \PPHP\tools\classes\standard\storage\database\DataMapper
   */
  protected $dataMapper;

  /**
   * Метод устанавливает Data Mapper данному классу для работы с СУБД.
   * @param \PPHP\tools\classes\standard\storage\database\DataMapper $dataMapper Интерфейс ORM.
   */
  public function setDataMapper(\PPHP\tools\classes\standard\storage\database\DataMapper $dataMapper){
    $this->dataMapper = $dataMapper;
  }

  /**
   * @return \PPHP\tools\classes\standard\storage\database\DataMapper
   */
  public function getDataMapper(){
    return $this->dataMapper;
  }

  /**
   * Метод устанавливает стандартные инструменты в качестве DataMapper.
   * @return \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticationManager Возвращает вызываемый объект для организации цепочек вызова.
   */
  public function useStandardConnectTools(){
    $this->dataMapper = \PPHP\services\database\DataMapperManager::getInstance()->getDataMapper();
    return $this;
  }

  /**
   * Метод пытается аутентифицировать сущность по заданной ключевой паре. Если сущность аутентифицирована, она восстанвливает свое последнее состояние.
   * @param \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticatedEntity $entity Аутентифицируемая сущность.
   * @return boolean true - если аутентификация успешна и сущность восстановлена, иначе - false.
   */
  public function authenticate(AuthenticatedEntity &$entity){
    $OID = $entity->getOID();
    $password = $entity->getPassword();

    try{
      $this->dataMapper->recoverFinding($entity, ['OID' => $OID, 'password' => $password]);
      return true;
    }
    catch(\PPHP\tools\classes\standard\storage\database\UncertaintyException $e){
      return false;
    }
  }

  /**
   * Метод позволяет зарегистрировать новую аутентифицируемую сущность в системе.
   * @throws \PPHP\tools\classes\standard\storage\database\QueryException Выбрасывается в случае, если запрос к БД выполнен с ошибкой.
   * @param AuthenticatedEntity $entity Регистрируемая сущность.
   */
  public function register(AuthenticatedEntity &$entity){
    $this->dataMapper->insert($entity);
  }
}
