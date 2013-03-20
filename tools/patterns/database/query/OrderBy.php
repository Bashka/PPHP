<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет условие сортировки результата запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class OrderBy implements ComponentQuery{
  /**
   * Используемые в сортировке поля.
   * @var \SplObjectStorage
   */
  private $fields;
  /**
   * Способ сортировки.
   * @var string
   */
  private $sortedType;

  /**
   * @param string $sortedType [ASC] Способ сортировки.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($sortedType = 'ASC'){
    if(array_search($sortedType, ['ASC', 'DESC']) == -1){
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается ASC или DESC.');
    }
    $this->fields = new \SplObjectStorage();
    $this->sortedType = $sortedType;
  }


  /**
   * Метод добавляет поле для сортировки.
   * @param Field $field Поле для сортировки.
   */
  public function addField(Field $field){
    $this->fields->attach($field);
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
    if($this->fields->count() == 0){
      throw new exceptions\NotFoundDataException('Недостаточно данных для формирования строки.');
    }
    $result = 'ORDER BY ';
    foreach($this->fields as $field){
      try{
        $result .= $field->interpretation($driver) . ',';
      }
      catch(exceptions\NotFoundDataException $exc){
        throw $exc;
      }
      catch(exceptions\InvalidArgumentException $exc){
        throw $exc;
      }
    }
    return substr($result, 0, strlen($result) - 1) . ' ' . $this->sortedType;
  }

}
