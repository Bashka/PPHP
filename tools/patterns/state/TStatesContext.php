<?php
namespace PPHP\tools\patterns\state;

/**
 * Реализует основные алгоритмы работы объектов, реализующих интерфейс StatesContext.
 *
 * <b>Важно:</b> Для полной реализации контекста, необходимо определить класс буфера состояний, инициализирующий и возвращающий требуемое в контексте состояние. Так же в конструкторе необходимо определить начальное состояние контекста.
 */
trait TStatesContext{

  /**
   * Текущее состояние объекта.
   * @var State $currentState
   */
  protected $currentState;
  /**
   * Буфер, хранящий все инициализированные состояния.
   * @var \PPHP\tools\patterns\state\StateBuffer
   */
  private $statesBuffer;

  /**
   * Метод может быть переопределен, для возврата массива ссылок на свойства объекта, доступ к которым разрешен из подсостояний.
   * @return array
   */
  protected function getLinksForState(){
    return [];
  }

  /**
   * Классическая реализация метода изменения состояния контекста. Данный метод передает буферу имя требуемого состояния, а так же массив со следующими компонентами:
   * context - ссылка на контекст;
   * links - null или массив доступных для состояния свойств контекста.
   *
   * @param $stateName
   * @param \PPHP\tools\patterns\state\State|\PPHP\tools\patterns\state\StatesContext $substate
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException
   */
  public function passageState($stateName, $substate){
    if($this->currentState === $substate || $this === $substate){
      $this->currentState = $this->statesBuffer->getState($stateName, $this, $this->getLinksForState());
    }
    else{
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Изменение состояния контекста не текущим состоянием запрещено.');
    }
  }

  public function getNameCurrentState(){
    return get_class($this->currentState);
  }
}