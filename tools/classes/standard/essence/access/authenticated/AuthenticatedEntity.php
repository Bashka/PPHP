<?php
namespace PPHP\tools\classes\standard\essence\access\authenticated;

use \PPHP\tools\patterns\database as database;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет аутентифицируемую сущность.
 * Аутентифицируемая сущность, представляемая данным классам, является доступной только при получении правильной ключевой пары.
 * Механизм аутентификации не является частью класса сущности, он вынесен в менеджер аутентификации и используется совместно с экземплярами данного класса и его потомков.
 * Все дочерние классы используют получаемую ключевую пару, для аутентификации запроса.
 * Класс может использоваться как родительский для таких сущностей, как: Учетная запись, Доступный по паролю файл, Доступный по паролю контент и т.д.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\essence\access\authenticated
 * @ORM\Table AuthenticatedEntity
 * @ORM\PK OID
 */
abstract class AuthenticatedEntity extends database\persistent\LongObject{
  /**
   * Пароль.
   * @var string
   * @ORM\ColumnName password
   */
  protected $password;

  protected function getSavedState(){
    return get_object_vars($this);
  }

  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      if(property_exists($this, $k) && $this::getReflectionProperty($k)->getDeclaringClass()->getName() === get_class()){
        $this->$k = $state[$k];
      }
    }
  }

  /**
   * Метод устанавливает пароль сущности.
   * @param string $password Устанавливаемый пароль.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setPassword($password){
    exceptions\InvalidArgumentException::verifyType($password, 'S');
    $this->password = $password;
  }

  /**
   * @return string
   */
  public function getPassword(){
    return $this->password;
  }
}

