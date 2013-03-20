<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Ограничитель выборки. Платформо-зависимый компонент.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Limit implements ComponentQuery{

  /**
   * Число отбираемых записей.
   * @var integer
   */
  private $countRow;

  /**
   * @param $countRow Число отбираемых записей.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($countRow){
    if(!is_int($countRow)){
      throw new exceptions\InvalidArgumentException('integer', $countRow);
    }
    $this->countRow = $countRow;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    if(!is_string($driver) || empty($driver)){
      throw new exceptions\InvalidArgumentException('string', $driver);
    }
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
        throw new exceptions\InvalidArgumentException('Недопустимое значение параметра. Ожидается sqlsrv, firebird, oci, mysql, pgsql или ibm.');
    }
  }
}
