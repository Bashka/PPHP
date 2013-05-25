<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter\Restorable;

/**
 * Ограничитель выборки. Платформо-зависимый компонент.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Limit extends ComponentQuery{
  /**
   * Число отбираемых записей.
   * @var integer
   */
  private $countRow;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['LIMIT '.self::getPatterns()['value']];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['value' => '[1-9][0-9]*'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

    $components = explode(' ', $string);
    // Выброс исключений невозможен.
    return new static((int) $components[1]);
  }

  /**
   * @param integer $countRow Число отбираемых записей.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или не положительного значения.
   */
  function __construct($countRow){
    exceptions\InvalidArgumentException::verifyType($countRow, 'i');
    exceptions\InvalidArgumentException::verifyVal($countRow, 'i > 0');
    $this->countRow = $countRow;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');

    switch($driver){
      case 'sqlsrv': // MS SQL Server
        return 'TOP '.$this->countRow;
      case 'firebird': // Firebird
        return 'FIRST '.$this->countRow;
      case 'oci': // Oracle
        return 'ROWNUM <= '.$this->countRow;
      case 'mysql': // MySQL
      case 'pgsql': // PostgreSQL
        return 'LIMIT '.$this->countRow;
      case 'ibm': // DB2
        return 'FETCH FIRST '.$this->countRow.' ROWS ONLY';
      default:
        throw exceptions\InvalidArgumentException::getValidException('sqlsrv|firebird|oci|mysql|pgsql|ibm', $driver);
    }
  }

  /**
   * @return integer
   */
  public function getCountRow(){
    return $this->countRow;
  }
}
