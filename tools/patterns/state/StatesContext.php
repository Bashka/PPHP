<?php
namespace PPHP\tools\patterns\state;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, реализующий данный интерфейс является контекстым, то есть может изменять свое поведение в зависимости от текущего состояния.
 * Как правило, такого рода класс делегирует поведение объектам-состояниям, что позволяет полиморфно заменять их при смене состояния.
 * В случае, если в качестве состояния установлен контекстный объект, то реализуется архитектура вложенного состояния объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
interface StatesContext{
  /**
   * Метод изменяет состояние объекта на заданное. Изменить состояние объекта может только его подсостояние, передаваемое во втором аргументе, или сам изменяемый объект. На практике это означает, что нет возможности изменить состояние объекта программно.
   * @abstract
   * @param string $stateName Устанавливаемое состояние.
   * @param \PPHP\tools\patterns\state\State|\PPHP\tools\patterns\state\StatesContext $substate Подсостояние, запрашивающее изменение.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Исключение выбрасывается при попытке программного изменения состояния.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия состояния с указанным именем.
   */
  public function passageState($stateName, $substate);

  /**
   * Метод возвращает имя текущего состояния объекта.
   * @abstract
   * @return string Имя текущего состояния.
   */
  public function getNameCurrentState();
}