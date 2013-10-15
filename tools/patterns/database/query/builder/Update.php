<?php
namespace PPHP\tools\patterns\database\query\builder;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс представляет фабрику объектной SQL инструкции Update.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query\builder
 */
class Update implements Singleton, Interpreter{
  use TSingleton;

  /**
   * @var \PPHP\tools\patterns\database\query\Update Объектная SQL инструкция Update.
   */
  protected $update;

  /**
   * @var \PPHP\tools\patterns\database\query\builder\Where|null Фабрика Where, являющаяся частью запроса, или null - если запрос не имеет условия.
   */
  protected $where;

  /**
   * Метод создает новую объектную SQL инструкцию Update.
   * @param string $table Имя целевой таблицы.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\builder\Delete Вызываемый объект.
   */
  public function table($table){
    InvalidArgumentException::verifyType($table, 'S');
    $this->update = new query\Update(new query\Table($table));

    return $this;
  }

  /**
   * Метод определяет обновляемые данные.
   * @param array $data Обновляемые данные в виде ассоциативного массива, ключами которого являются имена полей, а значениями входные данные.
   * @return \PPHP\tools\patterns\database\query\builder\Update Вызываемый объект.
   */
  public function data(array $data){
    foreach($data as $field => $value){
      $this->update->addData(new Field($field), $value);
    }

    return $this;
  }

  /**
   * Метод создает объектный SQL компонент Where для данного условия.
   * Метод должен быть вызван только после вызова метода table, формирующего инструкцию.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается если метод вызывается до вызова метода table.
   * @return \PPHP\tools\patterns\database\query\builder\Where Фабрика объектного SQL компонента Where для данной инструкции.
   */
  public function where($leftOperand, $operator, $rightOperand){
    if(empty($this->update)){
      throw new NotFoundDataException('Невозможно добавить условие отбора без указания целевой таблицы.');
    }
    try{
      /**
       * @var Where $whereBuilder
       */
      $whereBuilder = Where::getInstance();
      $this->where = $whereBuilder->create($leftOperand, $operator, $rightOperand);
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }
    $this->where->update = $this;

    return $this->where;
  }

  /**
   * Метод возвращает полученную объектную SQL инструкцию Update.
   * @return \PPHP\tools\patterns\database\query\Update Результат работы фабрики.
   */
  public function get(){
    if(isset($this->where)){
      $this->update->insertWhere($this->where->last());
      unset($this->where);
    }

    return $this->update;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    return $this->get()->interpretation($driver);
  }

  /**
   * Метод удаляет текущую конструкцию.
   */
  public function clear(){
    unset($this->update);
  }
}