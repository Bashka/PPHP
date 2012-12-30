<?php
namespace PPHP\tools\patterns\database\query;

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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($sortedType = 'ASC'){
    if(array_search($sortedType, ['ASC', 'DESC']) == -1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
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
   * @param string|null $driver Используемая СУБД.
   * @throws StandardException Выбрасывается в случае, если отсутствуют обязательные компоненты запроса.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if($this->fields->count() == 0){
      throw new StandardException();
    }
    $result = 'ORDER BY ';
    foreach($this->fields as $field){
      $result .= $field->interpretation() . ',';
    }
    return substr($result, 0, strlen($result) - 1) . ' ' . $this->sortedType;
  }

}
