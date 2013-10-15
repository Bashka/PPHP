<?php
namespace PPHP\tools\patterns\state;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса StatesContext.
 * Для полной реализации контекста, необходимо определить класс буфера состояний, инициализирующий и возвращающий требуемое в контексте состояние. Так же в конструкторе необходимо определить начальное состояние контекста.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
trait TStatesContext{
  /**
   * @var \PPHP\tools\patterns\state\State $currentState Текущее состояние объекта.
   */
  protected $currentState;

  /**
   * @var StateCache Кэш-фабрика, хранящая все инициализированные состояния.
   */
  private $statesFactory;

  /**
   * Метод может быть переопределен, для возврата массива ссылок на свойства объекта, доступ к которым разрешен из подсостояний.
   * @return mixed[]
   */
  protected function getLinksForState(){
    return [];
  }

  /**
   * Данный метод передает кэш-фабрике имя требуемого состояния, а так же массив со следующими компонентами:
   * context - ссылка на контекст;
   * links - null или массив доступных для состояния свойств контекста.
   * @prototype \PPHP\tools\patterns\state\StatesContext
   */
  public function passageState($stateName, $substate){
    // Контроль типа второго параметра перегружается контролем программного изменения состояния.
    if($this->currentState === $substate || $this === $substate){
      try{
        $this->currentState = $this->statesFactory->getState($stateName, $this, $this->getLinksForState());
      }
      catch(exceptions\InvalidArgumentException $e){
        throw $e;
      }
      catch(exceptions\NotFoundDataException $e){
        throw $e;
      }
    }
    else{
      throw new exceptions\RuntimeException('Изменение состояния контекста не текущим состоянием запрещено.');
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\state\StatesContext
   */
  public function getNameCurrentState(){
    return get_class($this->currentState);
  }
}