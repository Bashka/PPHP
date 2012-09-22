<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс-оболочка для добавления алиаса компоненту.
 */
abstract class Alias implements ComponentQuery{
  /**
   * Алиас компонента.
   * @var string
   */
  protected $alias;

  /**
   * Компонент.
   * @var ComponentQuery
   */
  protected $component;

  /**
   * @param $component
   * @param string $alias
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет недопустимый тип.
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
