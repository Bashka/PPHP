<?php
namespace PPHP\tools\patterns\database\associations;

/**
 * Класс представляет множество, которое может быть восстановлено из БД.
 *
 * Данный класс представляет множество ссылок на энергонезависимые объекты.
 * Экземпляр данного класса может находится в двух состояниях:
 * 1. Невостановленное состояние - ассоциация не содержит ссылок, а только SQL инструкцию, позволяющую восстановить ее;
 * 2. Востановленное состояние - ассоциация содержит ссылки.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\associations
 */
class LongAssociation extends \SplObjectStorage{
  /**
   * SQL инструкция, служащая для восстановления множества.
   * @var \PPHP\tools\patterns\database\query\Select
   */
  protected $selectQuery;
  /**
   * Отображение класса, являющегося основанием для восстановления. Ассоциация может включать ссылки только на экземпляры данного класса.
   * @var \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  protected $assocClass;

  /**
   * @param \PPHP\tools\patterns\database\query\Select $selectQuery SQL инструкция, служащая для восстановления множества.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass Отображение класса, являющегося основанием для восстановления.
   */
  function __construct(\PPHP\tools\patterns\database\query\Select $selectQuery, \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass){
    $this->selectQuery = $selectQuery;
    $this->assocClass = $assocClass;
  }

  /**
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  public function getAssocClass(){
    return $this->assocClass;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Select
   */
  public function getSelectQuery(){
    return $this->selectQuery;
  }
}