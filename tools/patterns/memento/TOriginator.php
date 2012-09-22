<?php
namespace PPHP\tools\patterns\memento;

trait TOriginator{
  /**
   * Метод должен возвращать ассоциативный массив значений свойств, которые будут записаны в хранитель.
   * Необходимо помнить, что этот метод позволяет решить проблемы, связанные с наследованием, в частности передать хранителю только те значения свойств объекта, которые доступны из его области видимости.
   * @abstract
   * @return array
   */
  abstract protected function getSavedState();

  public function createMemento(){
    return new Memento($this, $this->getSavedState());
  }

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
   * @param array $state
   */
  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      $this->$k = $v;
    }
  }
}
