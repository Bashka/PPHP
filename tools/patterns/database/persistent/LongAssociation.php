<?php
namespace PPHP\tools\patterns\database\persistent;

use PPHP\tools\patterns\database\query\Select;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Класс представляет множество ссылок на персистентные объекты, которое может быть восстановлено из БД.
 * Экземпляр данного класса может находится в двух состояниях:
 * 1. Невостановленное состояние - ассоциация не содержит ссылок, а только SQL инструкцию, позволяющую восстановить ее;
 * 2. Востановленное состояние - ассоциация содержит ссылки.
 * Алгоритм восстановления объектов данного класса является частью востанавливающего класса (DataMapper).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\associations
 */
class LongAssociation extends \SplObjectStorage{
  /**
   * @var Select SQL инструкция, служащая для восстановления множества.
   */
  protected $selectQuery;

  /**
   * @var ReflectionClass Отображение класса, являющегося основанием для восстановления. Ассоциация может включать ссылки только на экземпляры данного класса.
   */
  protected $assocClass;

  /**
   * @param Select $selectQuery SQL инструкция, служащая для восстановления множества.
   * @param ReflectionClass $assocClass Отображение класса, являющегося основанием для восстановления.
   */
  function __construct(Select $selectQuery, ReflectionClass $assocClass){
    $this->selectQuery = $selectQuery;
    $this->assocClass = $assocClass;
  }

  /**
   * @return ReflectionClass
   */
  public function getAssocClass(){
    return $this->assocClass;
  }

  /**
   * @return Select
   */
  public function getSelectQuery(){
    return $this->selectQuery;
  }
}