<?php
namespace PPHP\tools\patterns\io;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;

/**
 * Данный класс представляет классическую реализацию входного потока данных.
 * Дочернему классу достаточно реализовать метод read, использующий определенный здесь указатель на ресурс, остальные методы реализуются через использование этого метода.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class InStream implements Reader{
  /**
   * @var resource Указатель на ресурс, с которым работает объект.
   */
  protected $resource;

  /**
   * @param resource $resource Указатель на ресурс.
   */
  function __construct($resource){
    $this->resource = $resource;
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readString($length){
    InvalidArgumentException::verifyType($length, 'i');
    InvalidArgumentException::verifyVal($length, 'i > 0');
    $result = '';
    // Последовательное получение байт из потока.
    while($length--){
      try{
        $char = $this->read();
      }
      catch(IOException $exc){
        throw $exc;
      }
      $result .= $char;
      // Обнаружен конец потока.
      if($char === ''){
        break;
      }
    }

    return $result;
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readLine($EOLSymbol = PHP_EOL){
    InvalidArgumentException::verifyType($EOLSymbol, 'S');
    $EOLLength = strlen($EOLSymbol); // Длина символа EOL.
    $result = '';
    // Последовательное получение байт из потока.
    while(true){
      $portion = '';
      // Считывание пакета размером в символ EOL.
      for($i = $EOLLength; $i--;){
        // Получение текущего байта.
        try{
          $currentByte = $this->read();
        }
        catch(IOException $exc){
          throw $exc;
        }
        // Обнаружен конец потока.
        if($currentByte === ''){
          $result .= $portion;

          return $result;
        }
        // Формирование пакета.
        else{
          $portion .= $currentByte;
        }
      }
      // Обнаружение конца строки.
      if($portion == $EOLSymbol){
        return $result;
      }
      // Продолжение считывания.
      else{
        $result .= $portion;
      }
    }
    return '';
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readAll(){
    $result = '';
    // Последовательное получение байт из потока.
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
