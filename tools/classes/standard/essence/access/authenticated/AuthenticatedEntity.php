<?php
namespace PPHP\tools\classes\standard\essence\access\authenticated;

/**
 * Класс представляет аутентифицируемую сущность.
 * Восстановить состояние такой сущность возможно только при получении правильной ключевой пары.
 * Все дочерние классы используют получаемую ключевую пару, для аутентификации запроса.
 * Класс может использоваться как родительский для таких сущностей, как: Учетная запись, Доступный по паролю файл, Доступный по паролю контент и т.д.
 */
abstract class AuthenticatedEntity extends \PPHP\tools\patterns\database\LongObject{
  /**
   * Пароль.
   * @var string
   */
  protected $password;


  /**
   * @param string $password
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function setPassword($password){
    if(!is_string($password) || empty($password)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $password);
    }
    $this->password = $password;
  }

  /**
   * @return string
   */
  public function getPassword(){
    return $this->password;
  }
}

AuthenticatedEntity::getReflectionClass()->setMetadata('NameTable', 'AuthenticatedEntity');
AuthenticatedEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');

AuthenticatedEntity::getReflectionProperty('password')->setMetadata('NameFieldTable', 'password');

