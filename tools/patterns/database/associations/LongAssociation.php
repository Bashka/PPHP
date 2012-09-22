<?php
namespace PPHP\tools\patterns\database\associations;

/**
 * Класс представляет множество, которое может быть восстановлено из БД.
 */
class LongAssociation extends \SplObjectStorage{
  /**
   * Запрос, служащий для восстановления множества.
   * @var \PPHP\tools\patterns\database\query\Select
   */
  protected $selectQuery;
  /**
   * Отображение класса, являющегося основанием для восстановления.
   * @var \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  protected $assocClass;

  /**
   * @param \PPHP\tools\patterns\database\query\Select $selectQuery
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass
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