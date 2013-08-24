<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Предоставляет механизм перебора элементов множеств используя заданный шаг.
 */
class Pointer{
  /**
   * Начальная позиция указателя.
   * @var integer
   */
  private $startPoint;

  /**
   * Текущая позиция.
   * @var integer
   */
  private $position;

  /**
   * Длина шага.
   * @var integer
   */
  private $delta;

  /**
   * @param integer $startPoint Начальная позиция указателя.
   * @param integer $delta Длина шага.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если значение аргумента отлично от типа integer.
   */
  function __construct($startPoint, $delta){
    if(!is_integer($startPoint)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $startPoint);
    }
    if(!is_integer($delta)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $delta);
    }
    $this->startPoint = $startPoint;
    $this->position = $startPoint;
    $this->delta = $delta;
  }

  /**
   * Устанавливает указатель на заданный элемент.
   * @param integer $numLine Номер элемента начиная от 1.
   * @throws \OutOfRangeException Выбрасывается в случае, если значение аргумента выходит за границы допустимой области (является отрицательным).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если значение аргумента отлично от типа integer.
   */
  public function setLine($numLine){
    if(!is_integer($numLine)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $numLine);
    }
    $numLine--;
    if($numLine < 0){
      throw new \OutOfRangeException('Недопустимое значение аргумента. Требуется положительное значение большее нуля.');
    }
    $this->position = $this->startPoint + $numLine * $this->delta;
  }

  /**
   * Возвращает текущую позицию указателя.
   * @return integer Текущая позиция указателя.
   */
  public function getPosition(){
    return $this->position;
  }

  /**
   * Смещает указатель на одну позицию вперед.
   * @return boolean true - если указатель удачно смещен, иначе - false.
   */
  public function nextPosition(){
    $this->position += $this->delta;

    return true;
  }

  /**
   * Смещает указатель на одну позицию назад.
   * @return boolean true - если указатель удачно смещен, иначе - false.
   */
  public function prevPosition(){
    if($this->position - $this->delta >= $this->startPoint){
      $this->position -= $this->delta;

      return true;
    }
    else{
      return false;
    }
  }
}
