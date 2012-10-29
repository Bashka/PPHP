<?php
namespace PPHP\services\utilities;

/**
 * Класс отвечает за регистрацию, удаление и предоставление утилит в роутере.
 */
class UtilitiesRouter implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;

  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
  }

  /**
   * Метод возвращает контроллер для данной утилиты.
   * @param string $utilityName Имя утилиты.
   * @return string Имя контроллера.
   */
  public function getController($utilityName){
    return $this->conf->get('UtilitiesRouter', $utilityName);
  }

  /**
   * Метод определяет, существует ли данная утилита в системе.
   * @param string $utilityName Имя утилиты.
   * @return boolean true - если утилита установлена, иначе - false.
   */
  public function isUtilityExists($utilityName){
    return $this->conf->isExists('UtilitiesRouter', $utilityName);
  }

  /**
   * Метод добавляет новой путь в роутер.
   * @param string $utilityName Имя утилиты.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller Отображение класса контроллера для данной утилиты.
   */
  public function setController($utilityName, \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller){
    $this->conf->set('UtilitiesRouter', $utilityName, '\\'.$controller->getName());
  }

  /**
   * Метод удаляет данные утилиты из роутера.
   * @param string $utilityName Имя утилиты.
   * @return boolean true - если утилита была успешно удалена из роутера, иначе - false.
   */
  public function removeController($utilityName){
    return $this->conf->delete('UtilitiesRouter', $utilityName);
  }
}
