<?php
namespace PPHP\tools\patterns\state;

  /**
   * Определяет классы, которые изменяют свое поведение с изменением состояния.
   */
  interface StatesContext{
    /**
     * Метод изменяет состояние объекта на заданное.
     *
     * <b>Важно:</b> Изменить состояние объекта может только его подсостояние, передаваемое во втором аргументе. На практике это означает, что нет возможности изменить состояние объекта программно.
     * @abstract
     * @param string $stateName Устанавливаемое состояние.
     * @param \PPHP\tools\patterns\state\State|\PPHP\tools\patterns\state\StatesContext $substate Подсостояние, запрашивающее изменение.
     * @return void
     * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Исключение выбрасывается при попытке программного изменения состояния.
     */
    public function passageState($stateName, $substate);

    /**
     * Метод возвращает имя текущего состояния объекта.
     * @abstract
     * @return string Имя текущего состояния.
     */
    public function getNameCurrentState();
  }