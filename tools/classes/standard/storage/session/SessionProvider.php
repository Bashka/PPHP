<?php
namespace PPHP\tools\classes\standard\storage\session;

/**
 * Класс предоставляет интерфейс управления сессиями.
 */
class SessionProvider implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод открывает сессию.
   * @param string $sessionName Имя сессии.
   */
  public function start($sessionName = 'PHPSESSID'){
    if(session_status() == 1){
      session_name($sessionName);
      session_start();
    }
  }

  /**
   * Метод уничтожает сессию.
   */
  public function destroy(){
    if(session_status() != 1){
      $_SESSION = [];
      unset($_COOKIE[session_name()]);
      session_destroy();
    }
  }

  /**
   * Метод записывает данные в сессию.
   * @param string $key Ключ.
   * @param string|number|boolean $value Значение.
   */
  public function set($key, $value){
    $_SESSION[$key] = $value;
  }

  /**
   * Метод возвращает данные из сессии.
   * @param string $key Ключ.
   * @return string|null
   */
  public function get($key){
    return isset($_SESSION[$key])? $_SESSION[$key] : null;
  }

  /**
   * Метод удаляет данные из сессии.
   * @param string $key Ключ.
   * @return boolean true - если данные удачно удалены, false - если заданных данных не существует в сессии.
   */
  public function reset($key){
    if(!isset($_SESSION[$key])){
      return false;
    }
    unset($_SESSION[$key]);
    return true;
  }

  /**
   * Метод определяет имеются ли данные под заданным ключем в сессии.
   * @param string $key Ключ.
   * @return boolean true - если данные имеются, иначе - false.
   */
  public function isExists($key){
    return isset($_SESSION[$key]);
  }
}
