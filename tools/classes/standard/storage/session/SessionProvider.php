<?php
namespace PPHP\tools\classes\standard\storage\session;
use \PPHP\tools\patterns\singleton as singleton;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс предоставляет интерфейс управления сессиями.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\session
 */
class SessionProvider implements singleton\Singleton{
use singleton\TSingleton;

  /**
   * Метод открывает сессию.
   * @param string $sessionName Имя сессии.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function start($sessionName = 'PHPSESSID'){
    exceptions\InvalidArgumentException::verifyType($sessionName, 'S');

    if(session_status() == 1){
      session_name($sessionName);
      return session_start();
    }
  }

  /**
   * Метод уничтожает сессию.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function destroy(){
    if(session_status() != 1){
      $_SESSION = [];
      unset($_COOKIE[session_name()]);
      return session_destroy();
    }
  }

  /**
   * Метод записывает данные в сессию.
   * @param string $key Ключ.
   * @param string|number|boolean $value Значение.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function set($key, $value){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    $_SESSION[$key] = $value;
  }

  /**
   * Метод возвращает данные из сессии.
   * @param string $key Ключ.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return string|null Возвращает значение ключа сессии или null в случае отсутствия данных в сессии.
   */
  public function get($key){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    return isset($_SESSION[$key])? $_SESSION[$key] : null;
  }

  /**
   * Метод удаляет данные из сессии.
   * @param string $key Ключ.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если данные удачно удалены, false - если заданных данных не существует в сессии.
   */
  public function reset($key){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    if(!isset($_SESSION[$key])){
      return false;
    }
    unset($_SESSION[$key]);
    return true;
  }

  /**
   * Метод определяет имеются ли данные под заданным ключем в сессии.
   * @param string $key Ключ.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если данные имеются, иначе - false.
   */
  public function isExists($key){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    return isset($_SESSION[$key]);
  }
}
