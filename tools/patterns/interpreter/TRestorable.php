<?php
namespace PPHP\tools\patterns\interpreter;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Реализация интерфейса Restorable по средствам шаблонов и их сочетаний.
 * Данная реализация использует шаблоны регулярных выражений для поиска и обработки лексем строки-основания.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
trait TRestorable{
  /**
   * Метод последовательно применяет доступные шаблоны верификации к строке-основанию с целью определения шаблона, которому она соответствует, и поиска лексем.
   * @param string $string Строка-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику работы метода.
   * @return string[]|boolean Массив лексем, созданных первым подходящим шаблоном верификации. В качестве элемента с ключем key хранится ключ соответствующего шаблона верификации. В случае отсутствия подходящего шаблона верификации метод возвращает false.
   */
  private static function searchMask($string, $driver = null){
    static::updateString($string);
    foreach(static::getMasks($driver) as $key => $mask){
      $matches = [];
      if(preg_match('/^' . $mask . '$/u', $string, $matches)){
        $matches['key'] = $key;

        return $matches;
      }
    }

    return false;
  }

  /**
   * Метод должен возвращать массив шаблонов верификации, любому из которых должна соответствовать строка-основание.
   * В случае отсутствия соответствия, восстановление считается невозможным.
   * Возвращаемые шаблоны так же могут разделять строку-основание на лексемы для дальнейшей обработки.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику работы метода.
   * @return string[] Шаблоны верификации.
   */
  public static function getMasks($driver = null){
    return [];
  }

  /**
   * Метод может возвращать массив именованных лексем.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику работы метода.
   * @return string[] Массив именованных лексем.
   */
  public static function getPatterns($driver = null){
    return [];
  }

  /**
   * Метод позволяет определить допустимость восстановления объекта из строки-основания.
   * Метод последовательно применяет доступные шаблоны верификации к строке-основанию для поиска соответствия.
   * @param string $string Строка-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return boolean true - если интерпретация возможна, иначе - false.
   */
  public static function isReestablish($string, $driver = null){
    exceptions\InvalidArgumentException::verifyType($string, 's');
    if(!static::searchMask($string, $driver)){
      return false;
    }

    return true;
  }

  /**
   * Данный метод должен быть переопределен в реализующем классе для уточнения механизма восстановления.
   * Метод определяет подхощящий шаблон верификации и возвращает массив лексем, полученных из строки-основания. В качестве элемента с ключем key этого массива, указывается ключ первого подходящего шаблона верификации.
   * @abstract
   * @param string $string Строка-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации строки-основания.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\StructureException Выбрасывается в случае, если строка-основание не отвечает требования структуры.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string[]|boolean Массив лексем, созданных первым подходящим шаблоном верификации. В качестве элемента с ключем key хранится ключ соответствующего шаблона верификации. В случае отсутствия подходящего шаблона верификации метод возвращает false.
   */
  public static function reestablish($string, $driver = null){
    exceptions\InvalidArgumentException::verifyType($string, 'S');
    $mask = static::searchMask($string, $driver);
    if(!$mask){
      throw new exceptions\StructureException('Недопустимая структура для объекта ' . get_called_class() . ' [' . $string . '].');
    }

    return $mask;
  }

  /**
   * Данный метод вызывается автоматически методом searchMask и служит для подготовки строки-основания к верификации и поиску лексем.
   * Метод может быть переопределен конкретным классом, использующим данную реализацию.
   * Данный метод вызывается от имени вызываемого объекта.
   * @param string $string Строка-основание.
   */
  public static function updateString(&$string){
  }
}