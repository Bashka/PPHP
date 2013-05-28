<?php
namespace PPHP\model\classes;

use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Родительский класс, для всех внутренних инсталяторов.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\classes
 */
abstract class Installer implements Singleton{
  use TSingleton;
  /**
   * Метод настраивает систему для устанавливаемого модуля.
   * @abstract
   * @return string|void Информация об установке.
   */
  public abstract function install();

  /**
   * Метод отменяет изменения в системе при удалении модуля.
   * @abstract
   * @return string|void Информация об удалении.
   */
  public abstract function uninstall();
}


