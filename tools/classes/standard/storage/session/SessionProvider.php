<?php
namespace PPHP\tools\classes\standard\storage\session;

use \PPHP\tools\patterns\singleton as singleton;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет интерфейс управления сессиями.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\session
 */
class SessionProvider implements singleton\Singleton{
  use singleton\TSingleton;

  /**
   * Имя сессии по умолчанию.
   */
  const DEFAULT_SESSION_NAME = 'PHPSESSID';

  /**
   * Метод открывает сессию.
   * @param string $sessionName [optional] Имя сессии.
   * @param string $id [optional] Идентификатор сессии.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function start($sessionName = self::DEFAULT_SESSION_NAME, $id = null){
    exceptions\InvalidArgumentException::verifyType($sessionName, 'S');
    exceptions\InvalidArgumentException::verifyType($id, 'nS');
    if(session_status() == PHP_SESSION_NONE){
      if(!is_null($id)){
        session_id($id);
      }
      session_name($sessionName);

      return session_start();
    }

    return true;
  }

  /**
   * Метод возвращает идентификатор текущей сессии.
   * @return string Идентификатор сессии или пустая строка, если сессия не открыта.
   */
  public function getID(){
    return session_id();
  }

  /**
   * Метод возвращает имя текущей сессии.
   * @return string Имя текущей сессии или пустая строк, если сессия не открыта.
   */
  public function getName(){
    if(session_status() != PHP_SESSION_ACTIVE){
      return '';
    }
    return session_name();
  }

  /**
   * Метод уничтожает сессию.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function destroy(){
    if(session_status() == PHP_SESSION_ACTIVE){
      $_SESSION = [];
      unset($_COOKIE[session_name()]);

      return session_destroy();
    }

    return true;
  }

  /**
   * Метод записывает данные в сессию.
   * @param string $key Ключ.
   * @param string|number|boolean $value Значение.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function set($key, $value){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    $_SESSION[$key] = $value;
  }

  /**
   * Метод возвращает данные из сессии.
   * @param string $key Ключ.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return string|null Возвращает значение ключа сессии или null в случае отсутствия данных в сессии.
   */
  public function get($key){
    exceptions\InvalidArgumentException::verifyType($key, 'S');

    return isset($_SESSION[$key])? $_SESSION[$key] : null;
  }

  /**
   * Метод удаляет данные из сессии.
   * @param string $key Ключ.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если данные удачно удалены, false - если заданных данных не существует в сессии.
   */
  public function remove($key){
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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если данные имеются, иначе - false.
   */
  public function isExists($key){
    exceptions\InvalidArgumentException::verifyType($key, 'S');

    return isset($_SESSION[$key]);
  }

  public function __get($key){
    return $this->get($key);
  }

  public function __set($key, $value){
    $this->set($key, $value);
  }

  public function __isset($key){
    return $this->isExists($key);
  }

  public function __unset($key){
    $this->remove($key);
  }
}
