<?php
namespace PPHP\tools\patterns\memento;

/**
 * Определяет классы, способные сохранять и востанавливать текущее состояние с использованием хранителей.
 *
 * Реализующие данный интерфейс классы могут создавать "снимки" своих состояний используя хранителей.
 * Именно экземпляры классов, реализующих данный интерфейс, называются родителями хранителей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\memento
 */
interface Originator{
  /**
   * Метод создает хранителя с текущим состоянием объекта и возвращает его.
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
