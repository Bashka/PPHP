<?php
namespace PPHP\tools\patterns\io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация входного потока данных.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class InStream implements Reader{
  /**
   * @var resource Указатель на входной поток, с которым работает объект.
   */
  protected $resource;

  /**
   * @param resource $resource Указатель на входной поток.
   */
  function __construct($resource){
    $this->resource = $resource;
  }

  /**
   * Метод считывает указанное количество байт из потока.
   *
   * @param integer $length Количество считываемых байт.
   *
   * @throws IOException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Если размер входного потока больше или равен размеру считываемой строки, то прочитанная строка указанного размера, если меньше, то оставшаяся в потоке строка, если в потоке нет данных - пустая строка.
   */
  public function readString($length){
    if(!is_int($length)){
      throw new exceptions\InvalidArgumentException('integer', $length);
    }
    elseif($length < 1){
      throw new exceptions\InvalidArgumentException('Ожидается целочисленный параметр больший нуля.');
    }

    $result = '';
    while($length--){
      try{
        $char = $this->read();
      }
      catch(IOException $exc){
        throw $exc;
      }
      $result .= $char;
      if($char === ''){
        break;
      }
    }

    return $result;
  }

  /**
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   *
   * @param string $EOLSymbol [optional] Символ, принимаемый за EOL при данном вызове метода.
   *
   * @throws IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Прочитанная строка или пустая строка, если достигнут конец потока или символ EOL.
   */
  public function readLine($EOLSymbol = PHP_EOL){
    if(!is_string($EOLSymbol)){
      throw new exceptions\InvalidArgumentException('string', $EOLSymbol);
    }
    elseif($EOLSymbol === ''){
      throw new exceptions\InvalidArgumentException('Ожидается не пустая строка в качестве параметра.');
    }

    $EOLLength = strlen($EOLSymbol);
    $result = '';

    while(true){
      $portion = '';
      for($i = $EOLLength; $i--;){
        try{
          $currentByte = $this->read();
        }
        catch(IOException $exc){
          throw $exc;
        }

        if($currentByte === ''){
          $result .= $portion;
          return $result;
        }
        else{
          $portion .= $currentByte;
        }
      }
      if($portion == $EOLSymbol){
        return $result;
      }
      else{
        $result .= $portion;
      }
    }
  }

  /**
   * Метод считывает все содержимое потока.
   * @throws IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Прочитанный массив символов или пустая строка, если достигнут конец потока.
   */
  public function readAll(){
    $result = '';
    do{
      try{
        $currentByte = $this->read();
      }
      catch(IOException $exc){
        throw $exc;
      }
      $result .= $currentByte;
    } while($currentByte !== '');

    return $result;
  }
}
