<?php
namespace PPHP\tools\patterns\memento;

/**
 * Класс позволяет сохранять состояние объекта в себе и возвращать его по требованию хозяина.
 * С помощью объекто данного класса объект-хозяин может быть восстановлен в сохраненное состояние.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\memento
 */
class Memento{
  /**
   * @var mixed[] Ассоциативный массив имен и значений хранимых свойств.
   */
  private $properties;

  /**
   * @var Originator Хозяин хранителя.
   */
  private $originator;

  /**
   * @param Originator $originator Хозяин хранителя.
   * @param mixed[] $savedProperties Ассоциативный массив, ключами которого являются имена свойств хозяина, а значениями их значения.
   */
  function __construct(Originator $originator, array $savedProperties){
    $this->originator = $originator;
    $this->properties = $savedProperties;
  }

  /**
   * Метод возвращает хранимые значения свойств хранителю.
   * @param Originator $originator Хозяин хранителя. Метод вернет значения полей только если в данном аргументе передан истинный хозяин хранителя.
   * @throws \PPHP\tools\patterns\memento\AccessException Выбрасывается в случае, если в качестве хозяина передан не истинный хозяин хранителя.
   * @return mixed[] Ассоциативный массив значений полей хозяина.
   */
  public final function getState(Originator $originator){
    if($this->originator !== $originator){
      throw new AccessException('Доступ к хранимому состоянию запрещен.');
    }

    return $this->properties;
  }
}
