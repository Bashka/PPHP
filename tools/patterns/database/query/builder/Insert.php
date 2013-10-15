<?php
namespace PPHP\tools\patterns\database\query\builder;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс представляет фабрику объектной SQL инструкции Insert.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query\builder
 */
class Insert implements Singleton{
  use TSingleton;

  /**
   * @var \PPHP\tools\patterns\database\query\Insert Объектная SQL инструкция Insert.
   */
  protected $insert;

  /**
   * Метод создает новую объектную SQL инструкцию Insert.
   * @param string $table Имя целевой таблицы.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\builder\Insert Вызываемый объект.
   */
  public function table($table){
    InvalidArgumentException::verifyType($table, 'S');
    $this->insert = new query\Insert(new query\Table($table));

    return $this;
  }

  /**
   * Метод добавляет строку в инструкцию.
   * @param array $data Добавляемые данные в виде ассоциативного массива, ключами которого являются имена полей, а значениями входные данные.
   * @return \PPHP\tools\patterns\database\query\builder\Insert Вызываемый объект.
   */
  public function data(array $data){
    foreach($data as $field => $value){
      $this->insert->addData(new Field($field), $value);
    }

    return $this;
  }

  /**
   * Метод возвращает полученную объектную SQL инструкцию Insert.
   * @return \PPHP\tools\patterns\database\query\Insert Результат работы фабрики.
   */
  public function get(){
    return $this->insert;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    return $this->get()->interpretation($driver);
  }
}