<?php
  namespace PPHP\model\classes;

/**
 * Родительский класс, для всех внутренних инсталяторов.
 */
abstract class Installer implements \PPHP\tools\patterns\singleton\Singleton{
  use \PPHP\tools\patterns\singleton\TSingleton;
  /**
   * Метод настраивает систему для устанавливаемого модуля.
   * @abstract
   */
  public abstract function install();

  /**
   * Метод отменяет изменения в системе при удалении модуля.
   * @abstract
   * @return mixed
   */
  public abstract function uninstall();
}


