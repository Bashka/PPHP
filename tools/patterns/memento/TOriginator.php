<?php
namespace PPHP\tools\patterns\memento;

/**
 * Классическая реализация интерфейса PPHP\tools\patterns\memento\Originator.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\memento
 */
trait TOriginator{
  /**
   * Метод должен возвращать ассоциативный массив значений свойств, которые будут записаны в хранитель.
   * Необходимо помнить, что этот метод позволяет решить проблемы, связанные с наследованием, в частности передать хранителю только те значения свойств объекта, которые доступны из его области видимости.
   * @abstract
   * @return mixed[]
   */
  abstract protected function getSavedState();

  /**
   * Метод создает хранителя с текущим состоянием объекта и возвращает его.
   * @abstract
   * @return \PPHP\tools\patterns\memento\Memento
   */
  public function createMemento(){
    return new Memento($this, $this->getSavedState());
  }

  /**
   * Метод восстанавливает состояние вызываемого объекта из переданного хранителя.
   * @abstract
   * @param \PPHP\tools\patterns\memento\Memento $memento Хранитель, являющейся основой для восстановления.
   * @throws \PPHP\tools\patterns\memento\AccessException Выбрасывается в случае, если вызываемый объект пытается получить доступ к чужому хранителю.
   * @return boolean true - если востановление выполнено, иначе - false.
   */
  public function restoreFromMemento(Memento $memento){
    $state = $memento->getState($this);
    if(!is_array($state)){
      return false;
    }
    $this->setSavedState($state);
    return true;
  }

  /**
   * Метод позволяет восстановить состояние объекта по данным ассоциативного массива.
   * Необходимо понимать, что реализация данного метода в вершине иерархии классов делает private свойства дочерних классов недоступными для сохранения. Для решения этой проблемы, достаточно переопределить этот метод в дочернем классе.
   * @param mixed[] $state Ассоциативный массив, содержащий восстанавливаемое состояние объекта.
   */
  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      $this->$k = $v;
    }
  }
}
