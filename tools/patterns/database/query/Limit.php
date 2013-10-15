<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Ограничитель выборки. Платформо-зависимый компонент.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Limit extends ComponentQuery{
  /**
   * @var integer Число отбираемых записей.
   */
  private $countRow;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['LIMIT ' . self::getPatterns()['value']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['value' => '[1-9][0-9]*'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или не положительного значения.
   */
  function __construct($countRow){
    exceptions\InvalidArgumentException::verifyType($countRow, 'i');
    exceptions\InvalidArgumentException::verifyVal($countRow, 'i > 0');
    $this->countRow = $countRow;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    switch($driver){
      case 'sqlsrv': // MS SQL Server
        return 'TOP ' . $this->countRow;
      case 'firebird': // Firebird
        return 'FIRST ' . $this->countRow;
      case 'oci': // Oracle
        return 'ROWNUM <= ' . $this->countRow;
      case 'mysql': // MySQL
      case 'pgsql': // PostgreSQL
        return 'LIMIT ' . $this->countRow;
      case 'ibm': // DB2
        return 'FETCH FIRST ' . $this->countRow . ' ROWS ONLY';
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
