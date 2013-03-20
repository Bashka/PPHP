<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение метода класса, расширенное возможностью добавления метаданных.
 *
 * Данный класс является отображением метода с устойчивым состоянием и возможностью аннотирования.
 * Класс наследует все возможности своего родителя, что позволяет использовать его в контексте родительского класса.
 * Класс так же дополнен возможностью получения отражений своих аргументов с устойчивым состоянием и возможностью аннотирования.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionMethod extends \ReflectionMethod implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Множество отражений параметров метода.
   * Данный ассоциативный массив в качестве ключей использует имя аргумента (дублируется в порядковый номер), а в качестве значений их отражения.
   * @var ReflectionParameter[]
   */
  protected $reflectionParameters;

  /**
   * Метод возвращает отражение параметра метода.
   *
   * @param integer|string $param Порядковый индекс или имя параметра.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\LogicException Выбрасывается в случае, если указанного параметра не существует в методе.
   * @return ReflectionParameter Отражение параметра.
   */
  public function getParameter($param){
    if(!is_array($this->reflectionParameters)){
      $this->reflectionParameters = $this->getParameters();
      foreach($this->reflectionParameters as $k => &$v){
        $reflectionParameter = new ReflectionParameter([$this->getDeclaringClass()->getName(), $this->getName()], $v->getName());
        $this->reflectionParameters[$k] = $reflectionParameter;
        $this->reflectionParameters[$reflectionParameter->getName()] = & $this->reflectionParameters[$k];
      }
    }
    if(is_integer($param) || is_string($param)){
      if(array_key_exists($param, $this->reflectionParameters)){
        return $this->reflectionParameters[$param];
      }
      else{
        throw new exceptions\LogicException('Запрашиваемого параметра метода не существует.');
      }
    }
    else{
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается integer или string.');
    }
  }
}