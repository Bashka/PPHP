<?php
namespace PPHP\model\modules\System\Users;

/**
 * Класс предоставляет механизмы поддержания состояния сессии с клиентом.
 */
class SessionManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Менеджер аутентификации.
   * @var \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticationManager
   */
  protected $authManager;

  /**
   * Интерфейс взаимодействия с сессией.
   * @var \PPHP\tools\classes\standard\storage\session\SessionProvider
   */
  protected $sessionProvider;

  /**
   * @param \PPHP\tools\classes\standard\storage\session\SessionProvider $sessionProvider
   */
  public function setSessionProvider(\PPHP\tools\classes\standard\storage\session\SessionProvider $sessionProvider){
    $this->sessionProvider = $sessionProvider;
  }

  /**
   * Метод устанавливает стандартные инструменты в качестве SessionProvider.
   * @return \PPHP\model\modules\System\Users\SessionManager Возвращает вызываемый объект для организации цепочек вызова.
   */
  public function useStandardSessionTools(){
    $this->sessionProvider = \PPHP\tools\classes\standard\storage\session\SessionProvider::getInstance();
    return $this;
  }

  /**
   * @param \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticationManager $authManager
   */
  public function setAuthManager(\PPHP\tools\classes\standard\essence\access\authenticated\AuthenticationManager $authManager){
    $this->authManager = $authManager;
  }

  /**
   * Метод устанавливает стандартные инструменты в качестве DataMapper.
   * @return \PPHP\model\modules\System\Users\SessionManager Возвращает вызываемый объект для организации цепочек вызова.
   */
  public function useStandardAuthenTools(){
    $this->authManager = \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticationManager::getInstance();
    $this->authManager->useStandardConnectTools();
    return $this;
  }

  /**
   * Метод пытается идентифицировать пользователя по данным сессии.
   * @return DefaultUser|User DefaultUser - если пользователя не удалось идентифицировать, иначе - User.
   */
  public function identify(){
    $this->sessionProvider->start();
    if($this->sessionProvider->isExists('\PPHP\model\modules\System\Users\SessionManager::OID')){
      $user = new User();
      $user->restoreFromMemento(new \PPHP\tools\patterns\memento\Memento($user, ['OID' => $this->sessionProvider->get('\PPHP\model\modules\System\Users\SessionManager::OID')]));
      return $user;
    }
    else{
      return new DefaultUser();
    }
  }

  /**
   * Метод позволяет аутентифицировать клиента по предоставленной ключевой паре.
   * Если аутентификация успешна, сессия открывается автоматически.
   * @param integer $OID Идентификатор объекта.
   * @param string $password Представленный пароль.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   * @throws \PPHP\tools\classes\standard\essence\access\authenticated\AuthentifyException Выбрасывается в случае, если аутентификация не выполнена.
   * @return User Аутентифицированный пользователь.
   */
  public function authenticate($OID, $password){
    if(!is_integer($OID) || $OID === 0){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $OID);
    }
    if(!is_string($password) || empty($password)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $password);
    }
    $user = User::getProxy($OID);
    $user->setPassword($password);
    if($this->authManager->authenticate($user)){
      $this->sessionProvider->start();
      $this->sessionProvider->set('\PPHP\model\modules\System\Users\SessionManager::OID', $user->getOID());
      $user->clearPassword();
      return $user;
    }
    else{
      throw new \PPHP\tools\classes\standard\essence\access\authenticated\AuthentifyException('Аутентификация не пройдена.', 1);
    }
  }

  /**
   * Метод закрывает открытую сессию с клиентом.
   * @return boolean true - если сессия удачно закрыта, false - если сессия с клиентом не была открыта.
   */
  public function closeSession(){
    $this->sessionProvider->start();
    if($this->sessionProvider->isExists('\PPHP\model\modules\System\Users\SessionManager::OID')){
      $this->sessionProvider->reset('\PPHP\model\modules\System\Users\SessionManager::OID');
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * Метод регистрирует нового пользователя.
   * Пользователю автоматически присваивается идентификатор.
   * После регистрации аутентификации не производится.
   * @param string $password Используемый пароль.
   * @return User Зарегистрированный пользователь.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function register($password){
    if(!is_string($password) || empty($password)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $password);
    }
    $user = new User;
    $user->setPassword($password);
    $this->authManager->register($user);
    return $user;
  }
}