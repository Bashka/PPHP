<?php
namespace PPHP\tools\patterns\state;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса StatesContext.
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
   * @var StateBuffer
   */
  private $statesBuffer;

  /**
   * Метод может быть переопределен, для возврата массива ссылок на свойства объекта, доступ к которым разрешен из подсостояний.
   * @return mixed[]
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
   * @param string $stateName Устанавливаемое состояние.
   * @param State|StatesContext $substate Подсостояние, запрашивающее изменение.
   *
   * @throws exceptions\RuntimeException Исключение выбрасывается при попытке программного изменения состояния.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия состояния с указанным именем.
   */
  public function passageState($stateName, $substate){
    // Контроль типа второго параметра перегружается контролем программного изменения состояния
    if($this->currentState === $substate || $this === $substate){
      try{
        $this->currentState = $this->statesBuffer->getState($stateName, $this, $this->getLinksForState());
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
   * Метод возвращает имя текущего состояния объекта.
   * @return string Имя текущего состояния.
   */
  public function getNameCurrentState(){
    return get_class($this->currentState);
  }
}