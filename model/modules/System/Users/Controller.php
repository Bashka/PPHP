<?php
namespace PPHP\model\modules\System\Users;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод идентифицирует текущего пользователя.
   * @return User
   */
  public function identifyUser(){
    return SessionManager::getInstance()->useStandardSessionTools()->identify();
  }

  /**
   * Метод аутентифицирует пользователя по ключевой паре.
   * @param integer $id Идентификатор.
   * @param string $password Пароль.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Ключавая пара не прошла верификацию.
   * @throws \PPHP\tools\classes\standard\essence\access\authenticated\AuthentifyException Аутентификация не выполнена.
   * @return User Аутентифицированный пользователь.
   */
  public function authentifyUser($id, $password){
    return SessionManager::getInstance()->useStandardSessionTools()->useStandardAuthenTools()->authenticate((integer)$id, $password);
  }

  /**
   * Метод закрывает сессию с текущим пользователем.
   * @return boolean true - если сессия удачно закрыта, false - если сессия с клиентом не была открыта.
   */
  public function closeSession(){
    return SessionManager::getInstance()->useStandardSessionTools()->closeSession();
  }

  /**
   * Метод регистрирует нового пользователя.
   * @param string $password Пароль регистрируемого пользователя.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Ошибка верификации пароля.
   * @return User Зарегистрированный пользователь.
   */
  public function registerUser($password){
    return SessionManager::getInstance()->useStandardSessionTools()->useStandardAuthenTools()->register($password);
  }
}