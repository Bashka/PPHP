<?php
namespace PPHP\model\modules\System\Users;

/**
 * Представление пользователя.
 */
class User extends \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticatedEntity implements \JsonSerializable{
  protected $IP;

  function __construct(){
    if(isset($_SERVER['REMOTE_ADDR'])){
      $this->IP = $_SERVER['REMOTE_ADDR'];
    }
  }

  /**
   * @return array
   */
  public function JsonSerialize(){
    return ['OID' => $this->getOID()];
  }

  protected function getSavedState(){
    return get_object_vars($this);
  }

  public function setIP($IP){
    $this->IP = $IP;
  }

  public function getIP(){
    return $this->IP;
  }

  /**
   * Метод удаляет информацию о пароле пользователя.
   */
  public function clearPassword(){
    $this->password = null;
  }
}

User::getReflectionClass()->setMetadata('NameTable', 'Users_Users');
User::getReflectionClass()->setMetadata('KeyTable', 'OID');

User::getReflectionProperty('IP')->setMetadata('NameFieldTable', 'ip');