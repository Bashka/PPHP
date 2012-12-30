<?php
namespace PPHP\tools\patterns\io;

/**
 * Представление входного потока данных.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class InStream implements Reader{
  protected $resource;

  /**
   * @param resource $resource Указатель на входной поток.
   */
  function __construct($resource){
    $this->resource = $resource;
  }

  /**
   * Метод считывает указанное количество байт из потока.
   * @param integer $length Количество считываемых байт.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readString($length){
    if($length < 1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Ожидается целочисленный аргумент больший нуля.');
    }
    $result = '';
    while($length--){
      $char = $this->read();
      if($char === false){
        if($result == ''){
          return false;
        }
        else{
          $result .= $char;
          break;
        }
      }
      else{
        $result .= $char;
      }
    }

    return $result;
  }

  /**
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока (для данной функции концом потока так же являются символы \n и \r).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readLine(){
    $result = '';
    $beforeChar = '';
    while(($char = $this->read()) !== false){
      if($char === PHP_EOL || $beforeChar.$char === PHP_EOL){
        break;
      }
      $result .= $char;
      $beforeChar = $char;
    }
    $result = rtrim($result, "\n");
    $result = rtrim($result, "\r");
    return ($result == '' || $result == "\n" || $result == "\r")? false : $result;
  }

  /**
   * Метод считывает все содержимое потока.
   * @return boolean|string Прочитанный массив символов или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readAll(){
    $result = '';
    while(($char = $this->read()) !== false){
      $result .= $char;
    }
    return ($result == '')? false : $result;
  }
}
