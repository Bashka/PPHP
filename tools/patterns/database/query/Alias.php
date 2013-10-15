<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-оболочка для добавления алиаса компоненту.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class Alias extends ComponentQuery{
  /**
   * @var string Псевдоним компонента.
   */
  protected $alias;

  /**
   * @var \PPHP\tools\patterns\database\query\ComponentQuery Компонент, к которому устанавливается псевдоним.
   */
  protected $component;

  /**
   * Данный метод должен быть переопределен в дочерних классах и восстанавливать компонент alias.
   * @param string $string Исходная строка компонента.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\ComponentClassException Исключение свидетельствует о том, что дочерний класс не переопределил данный метод.
   * @return \PPHP\tools\patterns\database\query\ComponentQuery
   */
  protected static function reestablishChild($string, $driver = null){
    throw new exceptions\ComponentClassException('Дочерний класс не переопределил обязательный метод reestablishChild.');
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['aliasValue' => '[A-Za-z_][A-Za-z0-9_]*'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);
    /**
     * @var string[] $components
     */
    $components = explode(' as ', $string);
    try{
      return new static(static::reestablishChild($components[0]), $components[1]);
    }
    catch(exceptions\StructureException $e){
      throw $e;
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }
  }

  /**
   * @param ComponentQuery $component Компонент, к которому устанавливается псевдоним. Выбор конкретного типа компонента зависит от реализации.
   * @param string $alias Псевдоним компонента.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(ComponentQuery $component, $alias){
    exceptions\InvalidArgumentException::verifyType($alias, 'S');
    $this->alias = $alias;
    $this->component = $component;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
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

  /**
   * @return string
   */
  public function getAlias(){
    return $this->alias;
  }

  /**
   * @return ComponentQuery
   */
  public function getComponent(){
    return $this->component;
  }
}
