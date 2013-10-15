<?php
namespace PPHP\tools\patterns\memento;

/**
 * Определяет классы, способные сохранять и востанавливать текущее состояние с использованием хранителей.
 * Именно экземпляры классов, реализующих данный интерфейс, называются родителями хранителей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\memento
 */
interface Originator{
  /**
   * Создает хранителя со своим текущим состоянием и возвращает его.
   * @abstract
   * @return Memento Хранитель текущего состояния вызываемого объекта.
   */
  public function createMemento();

  /**
   * Восстанавливает состояние вызываемого объекта из переданного хранителя.
   * @abstract
   * @param Memento $memento Хранитель, являющийся основой для восстановления.
   * @throws \PPHP\tools\patterns\memento\AccessException Выбрасывается в случае, если вызываемый объект пытается получить доступ к чужому хранителю.
   */
  public function restoreFromMemento(Memento $memento);
}
