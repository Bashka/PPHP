<?php
namespace PPHP\tools\patterns\database\identification;

// @todo: Пересмотреть документацию класса для приведения к стандарту.
/**
 * Интерфейс определяет семантику генератора идентификационных номеров.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\identification
 */
interface OIDGenerator{
  /**
   * Метод генерирует новый идентификатор и возвращает его.
   * @throws OIDException Выбрасывается в случае невозможности генерации нового идентификатора.
   * @return integer Возвращает новый идентификатор.
   */
  public function generateOID();
}