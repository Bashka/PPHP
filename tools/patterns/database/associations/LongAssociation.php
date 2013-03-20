<?php
namespace PPHP\tools\patterns\database\associations;
use \PPHP\tools\patterns\database\query as query;
use \PPHP\tools\patterns\metadata\reflection as reflection;

/**
 * Класс представляет множество, которое может быть восстановлено из БД.
 *
 * Данный класс представляет множество ссылок на персистентные объекты.
 * Экземпляр данного класса может находится в двух состояниях:
 * 1. Невостановленное состояние - ассоциация не содержит ссылок, а только SQL инструкцию, позволяющую восстановить ее;
 * 2. Востановленное состояние - ассоциация содержит ссылки.
 * Алгоритм восстановления объектов данного класса является частью востанавливающего класса (DataMapper).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\associations
 */
class LongAssociation extends \SplObjectStorage{
  /**
   * SQL инструкция, служащая для восстановления множества.
   * @var query\Select
   */
  protected $selectQuery;
  /**
   * Отображение класса, являющегося основанием для восстановления. Ассоциация может включать ссылки только на экземпляры данного класса.
   * @var reflection\ReflectionClass
   */
  protected $assocClass;

  /**
   * @param query\Select $selectQuery SQL инструкция, служащая для восстановления множества.
   * @param reflection\ReflectionClass $assocClass Отображение класса, являющегося основанием для восстановления.
   */
  function __construct(query\Select $selectQuery, reflection\ReflectionClass $assocClass){
    $this->selectQuery = $selectQuery;
    $this->assocClass = $assocClass;
  }

  /**
   * @return reflection\ReflectionClass
   */
  public function getAssocClass(){
    return $this->assocClass;
  }

  /**
   * @return query\Select
   */
  public function getSelectQuery(){
    return $this->selectQuery;
  }
}