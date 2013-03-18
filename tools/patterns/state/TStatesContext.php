<?php
namespace PPHP\tools\patterns\state;

/**
 * Классическая реализация интерфейса \PPHP\tools\patterns\state\StatesContext.
 * Для полной реализации контекста, необходимо определить класс буфера состояний, инициализирующий и возвращающий требуемое в контексте состояние. Так же в конструкторе необходимо определить начальное состояние контекста.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
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
   * Метод изменяет состояние объекта на заданное. Изменить состояние объекта может только его подсостояние, передаваемое во втором аргументе. На практике это означает, что нет возможности изменить состояние объекта программно.
   * Данный метод передает буферу имя требуемого состояния, а так же массив со следующими компонентами:
   * context - ссылка на контекст;
   * links - null или массив доступных для состояния свойств контекста.
   *
   * @param $stateName Устанавливаемое состояние.
   * @param \PPHP\tools\patterns\state\State|\PPHP\tools\patterns\state\StatesContext $substate Подсостояние, запрашивающее изменение.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Исключение выбрасывается при попытке программного изменения состояния.
   * @return void
   */
  public function passageState($stateName, $substate){
    if($this->currentState === $substate || $this === $substate){
      $this->currentState = $this->statesBuffer->getState($stateName, $this, $this->getLinksForState());
    }
    else{
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Изменение состояния контекста не текущим состоянием запрещено.');
    }
  }

  /**
   * Метод возвращает имя текущего состояния объекта.
   * @return string Имя текущего состояния.
   */
  public function getNameCurrentState(){
    return get_class($this->currentState);
  }
}