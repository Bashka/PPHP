<?php
namespace PPHP\services\view;

/**
 * Класс отвечает за роутинг экранов.
 */
class ViewRouter implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  const SCREENS_DIR = 'PPHP/view/screens';

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;
  /**
   * @var \PPHP\services\cache\CacheAdapter
   */
  protected $cache;

  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
    $this->cache = \PPHP\services\cache\CacheSystem::getInstance();
    if(\PPHP\services\cache\CacheSystem::hasCache() && !isset($this->cache->ViewRouter_Init)){
      $screensNames = $this->getScreensNames();
      foreach($screensNames as $screenName){
        $this->cache->set('ViewRouter_Screens_' . $screenName, $this->conf->get('Screens', $screenName));
      }
      $this->cache->ViewRouter_Init = 1;
    }
  }

  /**
   * Метод возвращает заданный экран данного модуля.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @throws ScreenNotFoundException Выбрасывается в случае, если запрашиваемый экран не установлен.
   * @return string|boolean Адрес экрана относительно хранилища экранов или false - если указанный экран не установлен.
   */
  public function getScreen($moduleName, $screenName){
    $screen = null;
    if(($screen = $this->cache->get('ViewRouter_Screens_' . $moduleName . '_' . $screenName)) === false){
      if(!$this->conf->isExists('Screens', $moduleName . '_' . $screenName)){
        throw new ScreenNotFoundException($moduleName, $screenName);
      }
      $screen = $this->conf->get('Screens', $moduleName . '_' . $screenName);
      $this->cache->set('ViewRouter_Screens_' . $moduleName . '_' . $screenName, $screen);
    }
    return $screen;
  }

  /**
   * Метод определяет, задан ли данный экран.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return boolean true - если экран определен, иначе - false.
   */
  public function hasScreen($moduleName, $screenName){
    if($this->cache->get('ViewRouter_Screens_' . $moduleName . '_' . $screenName) === false){
      return $this->conf->isExists('Screens', $moduleName . '_' . $screenName);
    }
    return true;
  }

  /**
   * Метод задает экран модулю.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @param string $screen Расположение экрана относительно хранилища экранов.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если указанный экран уже установлен.
   */
  public function addScreen($moduleName, $screenName, $screen){
    if($this->hasScreen($moduleName, $screenName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Указанный экран ' . $moduleName . '::' . $screenName . ' уже установлен в системе.');
    }
    $this->conf->set('Screens', $moduleName . '_' . $screenName, $screen);
    $this->cache->set('ViewRouter_Screens_' . $moduleName . '_' . $screenName, $screen);
  }

  /**
   * Метод удаляет экран из роутинга.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если целевой экран не установлен в системе.
   * @return boolean true - если экран успешно удален из роутинга, иначе - false.
   */
  public function removeScreen($moduleName, $screenName){
    if(!$this->hasScreen($moduleName, $screenName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Указанный экран ' . $moduleName . '::' . $screenName . ' не установлен в системе.');
    }
    $this->cache->remove('ViewRouter_Screens_' . $moduleName . '_' . $screenName);
    return $this->conf->delete('Screens', $moduleName . '_' . $screenName);
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе экранов.
   * @return array Массив имен зарегистрированных в системе экранов.
   */
  public function getScreensNames(){
    $section = $this->conf->getSection('Screens');
    $screensNames = [];
    foreach($section as $screenName => $address){
      $screensNames[] = $screenName;
    }
    return $screensNames;
  }
}
