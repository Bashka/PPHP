<?php
namespace PPHP\tools\patterns\state;

/**
 * Класс реализует поведение состояний объектов.
 */
abstract class State{
  /**
   * Ссылка на конеткст состояния.
   * @var StatesContext
   */
  protected $context;
  /**
   * Массив ссылок на доступные состоянию свойства контекста.
   * @var array|StatesContext
   */
  protected $propertyLinks = [];

  /**
   * @param StatesContext $context Контекст состояния.
   * @param array|null $propertyLinks Массив ссылок на доступные состоянию свойства контекста.
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