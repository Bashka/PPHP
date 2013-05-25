<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет условие сортировки результата запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class OrderBy extends ComponentQuery{
  /**
   * Используемые в сортировке поля.
   * @var Field[]
   */
  private $fields;
  /**
   * Способ сортировки.
   * @var string
   */
  private $sortedType;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['ORDER BY (?:(?:'.Field::getMasks()[0].')|(?:'.Field::getMasks()[1].'))(?:, ?(?:(?:'.Field::getMasks()[0].')|(?:'.Field::getMasks()[1].')))* '.self::getPatterns()['types']];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['types' => '(?:ASC|DESC)'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

    $type = trim(substr($string, -4));
    $orderBy = new static($type);
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
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($sortedType = 'ASC'){
    exceptions\InvalidArgumentException::verifyVal($sortedType, 's # ASC|DESC');
    $this->fields = [];
    $this->sortedType = $sortedType;
  }

  /**
   * Метод добавляет поле для сортировки.
   * @param Field $field Поле для сортировки.
   */
  public function addField(Field $field){
    $this->fields[] = $field;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
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
   * @return \SplObjectStorage
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
