<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет поток, который может быть закрыт.
 * Закрытым потоком невозможно воспользоваться вновь.
 * Как правило закрытие потока сопровождается освобождением ресурсов системы, потому важно закрывать потоки, которые уже не будут использоваться.
 * При завершении сценария некоторые потоки могут быть закрыты автоматически.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Closed{
  /**
   * Метод закрывает поток.
   * @abstract
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае невозможности закрытия потока.
   * @return boolean true - если поток удачно закрыт, иначе - false.
   */
  public function close();

  /**
   * Метод определяет, закрыт ли поток.
   * @abstract
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose();
}
