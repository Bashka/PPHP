<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет условие сортировки результата запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class OrderBy extends ComponentQuery{
  /**
   * Маркер для сортировки по возрастанию.
   */
  const ASC = 'ASC';

  /**
   * Маркер для сортировки по убыванию.
   */
  const DESC = 'DESC';

  /**
   * @var \PPHP\tools\patterns\database\query\Field[] Используемые в сортировке поля.
   */
  private $fields;

  /**
   * @var string Способ сортировки.
   */
  private $sortedType;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['ORDER BY (?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . '))(?:, ?(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')))* ' . self::getPatterns()['types']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['types' => '(?:ASC|DESC)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);
    $type = trim(substr($string, -4));
    $orderBy = new OrderBy($type);
    $fields = explode(',', substr(substr($string, 9), 0, -4));
    foreach($fields as $field){
      $field = trim($field);
      try{
        $orderBy->addField(Field::reestablish($field));
      }
      catch(exceptions\StructureException $e){
        throw $e;
      }
      catch(exceptions\InvalidArgumentException $e){
        throw $e;
      }
    }

    return $orderBy;
  }

  /**
   * @param string $sortedType [ASC] Способ сортировки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($sortedType = self::ASC){
    exceptions\InvalidArgumentException::verifyVal($sortedType, 's # ASC|DESC');
    $this->fields = [];
    $this->sortedType = $sortedType;
  }

  /**
   * Метод добавляет поле для сортировки.
   * @param \PPHP\tools\patterns\database\query\Field $field Поле для сортировки.
   */
  public function addField(Field $field){
    $this->fields[] = $field;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if(count($this->fields) == 0){
      throw new exceptions\NotFoundDataException('Недостаточно данных для формирования строки.');
    }
    $result = 'ORDER BY ';
    foreach($this->fields as $field){
      $result .= $field->interpretation($driver) . ',';
    }

    return substr($result, 0, strlen($result) - 1) . ' ' . $this->sortedType;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Field[]
   */
  public function getFields(){
    return $this->fields;
  }

  /**
   * @return string
   */
  public function getSortedType(){
    return $this->sortedType;
  }
}
