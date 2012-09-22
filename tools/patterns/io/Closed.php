<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет поток, который может быть закрыт.
 */
interface Closed{
  /**
   * Метод закрывает данный поток.
   * @abstract
   * @return boolean true - если поток удачно закрыт, иначе - false.
   */
  public function close();

  /**
   * Метод проверяет, закрыт ли поток.
   * @abstract
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose();
}
