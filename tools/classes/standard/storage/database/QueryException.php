<?php
namespace PPHP\tools\classes\standard\storage\database;

/**
 * Данное исключение свидетельствует о том, что запрос к БД был выполнен с ошибкой.
 */
class QueryException extends \PPHP\tools\classes\standard\baseType\exceptions\PDOException{
  protected $number;

  public function __construct($message = "", $code = 0, \Exception $previous = null, $number){
    parent::__construct($message, $code, $previous);
    $this->number = $number;
  }

  public function getNumber(){
    return $this->number;
  }
}
