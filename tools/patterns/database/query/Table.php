<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет таблицу в запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Table implements ComponentQuery{
  /**
   * Имя таблицы.
   * @var string
   */
  private $tableName;

  /**
   * @param string $tableName Имя таблицы.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($tableName){
    if(!is_string($tableName) || empty($tableName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $tableName);
    }
    $this->tableName = $tableName;
  }


  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return $this->tableName;
  }

}
