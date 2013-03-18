<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Отражение метода класса, расширенное возможностью добавления метаданных.
 *
 * Данный класс является отображением метода с устойчивым состоянием и возможностью аннотирования.
 * Класс наследует все возможности своего родителя, что позволяет использовать его в контексте родительского класса.
 * Класс так же дополнен возможностью получения отражений своих аргументов с устойчивым состоянием и возможностью аннотирования.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionMethod extends \ReflectionMethod implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;

  /**
   * Множество отражений параметров метода.
   * @var ReflectionParameter[]
   */
  protected $reflectionParameters;

  /**
   * Метод возвращает отражение параметра метода.
   * @param integer|string $param Порядковый индекс или имя параметра.
   * @return ReflectionParameter Отражение параметра.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если указанного параметра не существует в методе.
   */
  public function getParameter($param){
    if(!is_array($this->reflectionParameters)){
      $this->reflectionParameters = $this->getParameters();
      foreach($this->reflectionParameters as $k => &$v){
        $reflectionParameter = new ReflectionParameter([$this->getDeclaringClass()->getName(), $this->getName()], $v->getName());
        $this->reflectionParameters[$k] = $reflectionParameter;
        $this->reflectionParameters[$reflectionParameter->getName()] = &$this->reflectionParameters[$k];
      }
    }
    if(is_integer($param) || is_string($param)){
      if(array_key_exists($param, $this->reflectionParameters)){
        return $this->reflectionParameters[$param];
      }
      else{
        throw new \PPHP\tools\classes\standard\baseType\exceptions\LogicException('Запрашиваемого параметра метода не существует.');
      }
    }
    else{
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается integer или string.');
    }
  }
}