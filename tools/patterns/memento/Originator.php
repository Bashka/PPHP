<?php
namespace PPHP\tools\patterns\memento;

/**
 * Определяет классы, способные сохранять и востанавливать текущее состояние с использованием хранителей.
 */
interface Originator{
  /**
   * Метод создает хранителя с текущим состоянием вызываемого объекта и возвращает его.
   * @abstract
   * @return \PPHP\tools\patterns\memento\Memento
   */
  public function createMemento();

  /**
   * Метод восстанавливает состояние вызываемого объекта из переданного хранителя.
   * @abstract
   * @param \PPHP\tools\patterns\memento\Memento $memento Хранитель, являющейся основой для восстановления.
   * @throws \PPHP\tools\patterns\memento\AccessException Выбрасывается в случае, если вызываемый объект пытается получить доступ к чужому хранителю.
   * @return boolean true - если востановление выполнено, иначе - false.
   */
  public function restoreFromMemento(Memento $memento);
}
