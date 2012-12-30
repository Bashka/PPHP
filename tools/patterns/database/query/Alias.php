<?php
namespace PPHP\tools\patterns\database\query;

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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($component, $alias){
    if(!is_string($alias)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $alias);
    }
    $this->alias = $alias;
    $this->component = $component;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return $this->component->interpretation() . ' as ' . $this->alias;
  }
}
