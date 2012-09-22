<?php
namespace PPHP\model\modules\System\Users\tests;

class MockSessionProvider extends \PPHP\tools\classes\standard\storage\session\SessionProvider{
  protected $storage = [];


  public function start($sessionName = 'PHPSESSID'){}

  public function set($key, $value){
    $this->storage[$key] = $value;
  }

  public function get($key){
    if(!isset($this->storage[$key]))
      return null;
    return $this->storage[$key];
  }

  public function reset($key){
    if(isset($this->storage[$key])){
      unset($this->storage[$key]);
    }
  }

  public function isExists($key){
    return isset($this->storage[$key]);
  }

  public function destroy(){
    $this->storage = [];
  }

  public function getStorage(){
    return $this->storage;
  }
}