<?php
namespace PPHP\services\view;

/**
 * Класс отвечает за роутинг экранов.
 */
class ViewRouter implements ViewRouterInterface, \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;

  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
  }
  /**
   * Метод возвращает заданный экран данного модуля.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return string Адрес экрана
   */
  public function getScreen($moduleName, $screenName){
    return $this->conf->get('ViewRouter', $moduleName.'_'.$screenName);
  }

  /**
   * Метод определяет, задан ли данный экран.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return boolean true - если экран определен, иначе - false.
   */
  public function isScreenExists($moduleName, $screenName){
    return $this->conf->isExists('ViewRouter', $moduleName.'_'.$screenName);
  }

  /**
   * @abstract
   * Метод задает экран модулю.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @param string $screen Расположение экрана.
   */
  public function setScreen($moduleName, $screenName, $screen){
    $this->conf->set('ViewRouter', $moduleName.'_'.$screenName, $screen);
  }

  /**
   * Метод удаляет экран из роутинга.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return boolean true - если экран успешно удален из роутинга, иначе - false.
   */
  public function removeScreen($moduleName, $screenName){
    return $this->conf->delete('ViewRouter', $moduleName.'_'.$screenName);
  }
}
