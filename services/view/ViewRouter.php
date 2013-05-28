<?php
namespace PPHP\services\view;
use PPHP\services\cache\CacheAdapter;
use PPHP\services\cache\CacheSystem;
use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use \PPHP\tools\patterns\singleton as singleton;
/**
 * Класс отвечает за роутинг экранов.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\view
 */
class ViewRouter implements singleton\Singleton{
  use singleton\TSingleton;

  /**
   * Адрес хранилища экранов.
   */
  const SCREENS_DIR = 'PPHP/view/screens';

  /**
   * Конфигуратор системы.
   * @var Configurator
   */
  protected $conf;
  /**
   * Кэш.
   * @var CacheAdapter
   */
  protected $cache;
  /**
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   */
  private function __construct(){
    try{
      $this->conf = Configurator::getInstance();
      $this->cache = CacheSystem::getInstance();
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
    if(CacheSystem::hasCache() && !isset($this->cache->ViewRouter_Init)){
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
    if(($screen = $this->cache->get('ViewRouter_Screens_' . $moduleName . '_' . $screenName)) === null){
      if(!$this->conf->isExists('Screens', $moduleName . '_' . $screenName)){
        throw ScreenNotFoundException::getException($moduleName, $screenName);
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
    if($this->cache->get('ViewRouter_Screens_' . $moduleName . '_' . $screenName) === null){
      return $this->conf->isExists('Screens', $moduleName . '_' . $screenName);
    }
    return true;
  }

  /**
   * Метод задает экран модулю.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @param string $screen Расположение экрана относительно хранилища экранов.
   * @throws ScreenDuplicationException Выбрасывается в случае, если указанный экран уже установлен.
   */
  public function addScreen($moduleName, $screenName, $screen){
    if($this->hasScreen($moduleName, $screenName)){
      throw ScreenDuplicationException::getException($moduleName, $screenName);
    }
    $this->conf->set('Screens', $moduleName . '_' . $screenName, $screen);
    $this->cache->set('ViewRouter_Screens_' . $moduleName . '_' . $screenName, $screen);
  }

  /**
   * Метод удаляет экран из роутинга.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @throws ScreenNotFoundException Выбрасывается в случае, если целевой экран не установлен в системе.
   * @return boolean true - если экран успешно удален из роутинга, иначе - false.
   */
  public function removeScreen($moduleName, $screenName){
    if(!$this->hasScreen($moduleName, $screenName)){
      throw new ScreenNotFoundException('Указанный экран ' . $moduleName . '::' . $screenName . ' не установлен в системе.');
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
