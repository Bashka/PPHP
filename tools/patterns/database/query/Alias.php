<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter\Restorable;

/**
 * Класс-оболочка для добавления алиаса компоненту.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class Alias extends  ComponentQuery{
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
   * Данный метод должен быть переопределен в дочерних классах и восстанавливать компонент alias.
   * @param string $string Исходная строка компонента.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return ComponentQuery
   */
  protected static function reestablishChild($string, $driver = null){
    throw new exceptions\ComponentClassException('Дочерний класс не переопределил обязательный метод reestablishChild.');
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return [
      'aliasValue' => '[A-Za-z_][A-Za-z0-9_]*'
    ];
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
   * @param $component Компонент, к которому устанавливается псевдоним. Выбор конкретного типа компонента зависит от реализации.
   * @param string $alias Псевдоним компонента.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($component, $alias){
    exceptions\InvalidArgumentException::verifyType($alias, 'S');
    $this->alias = $alias;
    $this->component = $component;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
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
