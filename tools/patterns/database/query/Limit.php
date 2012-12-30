<?php
namespace PPHP\tools\patterns\database\query;

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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($countRow){
    if(!is_int($countRow)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $countRow);
    }
    $this->countRow = $countRow;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if(!is_string($driver) || empty($driver)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $driver);
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
        throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
    }
  }
}
