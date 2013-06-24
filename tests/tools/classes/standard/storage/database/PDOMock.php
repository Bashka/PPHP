<?php
namespace PPHP\tests\tools\classes\standard\storage\database;

use PPHP\tools\classes\standard\storage\database\PDO;

class PDOMock extends PDO{
  public $driver = 'mysql';

  public $queries = [];

  public $restore = [];

  public function __construct($dsn, $username, $passwd, $options){
  }

  public function getAttribute($attribute){
    return $this->driver;
  }

  public function query($statement){
    $this->queries[] = $statement;

    return array_shift($this->restore);
  }

  public function multiQuery($script, $delimiter = null){
    foreach($script as $query){
      $this->query($query->interpretation($this->getAttribute(PDO::ATTR_DRIVER_NAME)));
    }
  }
}