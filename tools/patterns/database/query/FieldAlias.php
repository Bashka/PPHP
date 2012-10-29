<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс позволяет добавить псевдоним полю.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class FieldAlias extends Alias{
  /**
   * @param $component Поле, к которому добавляется псевдоним.
   * @param string $alias Псевдоним поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($component, $alias){
    if(!($component instanceof Field)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Field', $component);
    }
    parent::__construct($component, $alias);
  }
}
