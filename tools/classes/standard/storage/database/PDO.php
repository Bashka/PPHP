<?php
namespace PPHP\tools\classes\standard\storage\database;

/**
 * Данный класс реагирует выбросом исключения в ответ на ошибку в запросе.
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
   * @throws QueryException Выбрасывается в случае ошибки, во время выполнения запроса.
   * @return \PDOStatement <b>PDO::query</b> returns a PDOStatement className, or false
   * on failure.
   */
  public function query($statement){
    $resultQuery = parent::query($statement);
    if((int) $this->errorCode() != 0){
      throw new QueryException($this->errorInfo()[2], (int) $this->errorInfo()[0], null, $this->errorInfo()[1]);
    }
    return $resultQuery;
  }

  /**
   * Метод выполняет в транзакции множественный SQL скрипт, компоненты которого разделены некоторым символом-разделителем.
   * @param string|array $script Выполняемый SQL скрипт в виде строки или массива.
   * @param string|null $delimiter Символ-разделитель если скрипт передан в виде строки.
   * @throws QueryException Выбрасывается в случае, если в процессе транзакции произошла ошибка.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function multiQuery($script, $delimiter = null){
    if(is_string($script)){
      $queries = explode($delimiter, $script);
    }
    elseif(is_array($script)){
      $queries = $script;
    }
    else{
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Неверный тип аргумента, ожидается string или array.');
    }
    $this->beginTransaction();
    foreach($queries as $query){
      $query = trim($query);
      if(!empty($query)){
        try{
          $this->query($query);
        }
        catch(QueryException $exc){
          $this->rollBack();
          throw $exc;
        }
      }
    }
    $this->commit();
  }
}
