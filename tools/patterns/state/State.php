<?php
namespace PPHP\tools\patterns\state;

/**
 * Класс реализует поведение состояний объектов.
 * Дочерние классы являются состояниями некоторого объекта-контекста, позволяя изменять его поведение через делегирование вызова.
 * Каждый объект-состояние необходимо реализовывать с использованием семантики объекта-контектса, чтобы механизм делегирования работал верно.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
abstract class State{
  /**
   * @var StatesContext Ссылка на конеткст состояния.
   */
  protected $context;

  /**
   * @var mixed[] Массив ссылок на доступные состоянию свойства контекста.
   */
  protected $propertyLinks = [];

  /**
   * @param StatesContext $context Контекст состояния.
   * @param mixed[] $propertyLinks [optional] Массив ссылок на доступные состоянию свойства контекста.
   */
  public function __construct(StatesContext $context, array &$propertyLinks = null){
    $this->context = $context;
    if(!is_null($propertyLinks)){
      foreach($propertyLinks as $k => &$v){
        $this->propertyLinks[$k] = $v;
      }
    }
  }
}