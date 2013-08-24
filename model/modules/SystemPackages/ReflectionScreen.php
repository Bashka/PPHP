<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\services\view\ViewRouter;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\patterns\metadata\TDescribed;

/**
 * Отражение экрана.
 * Данный класс является отражением экрана системы с устойчивым состоянием и возможностью аннотирования.
 * Класс может быть инстанциирован только для установленных в системе экранов.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class ReflectionScreen extends ReflectionSystemComponent{
  /**
   * Метод возвращает имя модуля, которому принадлежит экран.
   * @return string Имя модуля экрана.
   */
  public function getModuleName(){
    return explode(':', $this->getName())[0];
  }

  /**
   * Метод возвращает имя экрана.
   * @return string Имя экрана.
   */
  public function getScreenName(){
    return explode(':', $this->getName())[1];
  }

  /**
   * Метод возвращает физический адрес экрана относительно корня системы.
   * @return string Физический адрес экрана относительно корня системы.
   */
  public function getAddress(){
    return '/' . ViewRouter::SCREENS_DIR . '/' . $this->getModuleName() . '/' . $this->getScreenName() . '/';
  }
}
