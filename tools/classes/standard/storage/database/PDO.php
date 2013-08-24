<?php
namespace PPHP\tools\classes\standard\storage\database;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query\ComponentQuery;

/**
 * Данный класс представляет расширенный PDO интерфейс. Класс реагирует выбросом исключения в ответ на ошибку в запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database
 */
class PDO extends \PDO{
  /**
   * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.2.0)<br/>
   * Executes an SQL statement, returning a result set as a PDOStatement className
   * @link http://php.net/manual/en/pdo.query.php
   * @param string $statement <p>
   * The SQL statement to prepare and execute.
   * </p>
   * <p>
   * Data inside the query should be properly escaped.
   * </p>
   * @throws exceptions\PDOException Выбрасывается в случае ошибки, во время выполнения запроса.
   * @return \PDOStatement <b>PDO::query</b> returns a PDOStatement className, or false
   * on failure.
   */
  public function query($statement){
    $resultQuery = parent::query($statement);
    if((int) $this->errorCode() != 0){
      throw new exceptions\PDOException($this->errorInfo()[2], (int) $this->errorInfo()[0]);
    }

    return $resultQuery;
  }

  /**
   * Метод выполняет в транзакции множественный SQL скрипт, компоненты которого разделены некоторым символом-разделителем.
   * @param string|string[]|ComponentQuery $script Выполняемый SQL скрипт в виде строки, массива строк или объектных представлений запросов.
   * @param string|null $delimiter Символ-разделитель если скрипт передан в виде строки.
   * @throws exceptions\PDOException Выбрасывается в случае, если в процессе транзакции произошла ошибка.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function multiQuery($script, $delimiter = null){
    if(is_string($script)){
      // Подготовка строки
      $queries = explode($delimiter, $script);
    }
    elseif(is_array($script)){
      // Подготовка массива
      $queries = $script;
    }
    else{
      throw exceptions\InvalidArgumentException::getTypeException(['string', 'array'], gettype($script));
    }
    $this->beginTransaction();
    foreach($queries as $query){
      if($query instanceof ComponentQuery){
        $query = $query->interpretation($this->getAttribute(PDO::ATTR_DRIVER_NAME));
      }
      $query = trim($query);
      if(!empty($query)){
        try{
          $this->query($query);
        }
        catch(exceptions\PDOException $exc){
          $this->rollBack();
          throw $exc;
        }
      }
    }
    $this->commit();
  }
}
