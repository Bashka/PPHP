<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-оболочка для добавления алиаса компоненту.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class Alias implements ComponentQuery{
  /**
   * Псевдоним компонента.
   * @var string
   */
  protected $alias;

  /**
   * Компонент, к которому устанавливается псевдоним.
   * @var ComponentQuery
   */
  protected $component;

  /**
   * @param $component Компонент, к которому устанавливается псевдоним.
   * @param string $alias Псевдоним компонента.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($component, $alias){
    if(!is_string($alias)){
      throw new exceptions\InvalidArgumentException('string', $alias);
    }
    $this->alias = $alias;
    $this->component = $component;
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
    try{
      return $this->component->interpretation($driver) . ' as ' . $this->alias;
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }
}
