<?php
namespace PPHP\tests\tools\classes\standard\storage\database;


class MockDataMapper extends \PPHP\tools\classes\standard\storage\database\DataMapper{
  /**
   * Множество команд, переданных объекту в процессе работы. Порядок сохранен.
   * @var string[]
   */
  protected $commands = [];

  /**
   * Множество данных, возвращаемых или устанавливаемых методами объекта.
   * @var array
   */
  protected $returns = [];

  /**
   * Метод обновляет объект.
   */
  public function reset(){
    $this->commands = [];
    $this->returns = [];
  }

  /**
   * Метод возвращает заданную команду.
   * @param integer $i Порядок команды.
   * @return string
   */
  public function getCommand($i){
    return $this->commands[$i];
  }

  /**
   * Метод устанавливает реакции объекта.
   * @param array $returns Множество реакций расположенных по порядку их вызова.
   */
  public function setReturns(array $returns){
    $this->returns = $returns;
  }

  /**
   * @param \PPHP\tools\patterns\database\LongObject $object
   * @return string
   */
  protected function toStringObject(\PPHP\tools\patterns\database\LongObject $object){
    $string = get_class($object) . '[';
    $state = $object->createMemento()->getState($object);
    foreach($state as $k => &$v){
      $v = $k . '=' . (($v instanceof \PPHP\tools\patterns\database\LongObject)? $v->getLinkOID() : (($v instanceof \PPHP\tools\patterns\database\associations\LongAssociation)? 'LongAssociation': $v));
    }
    $string .= implode(',', $state);
    $string .= ']';
    return $string;
  }

  /**
   * Метод записывает имя команды и состояние объекта.
   * Команда имеет сдедующую структуру:
   * insert(полноеИмяКлассаОбъекта[свойство=значение,свойство=значение,...])
   *
   * Массив ответа используется в следующем порядке:
   * 1. integer - устанавливается идентификатор переданной сущности.
   *
   * @param \PPHP\tools\patterns\database\LongObject $object
   */
  public function insert(\PPHP\tools\patterns\database\LongObject &$object){
    $this->commands[] = 'insert(' . $this->toStringObject($object) . ')';

    $data = array_shift($this->returns);
    if(!empty($data)){
      $object->setOID($data);
    }
  }

  /**
   * Метод записывает имя команды и состояние объекта.
   * Команда имеет сдедующую структуру:
   * delete(полноеИмяКлассаОбъекта[свойство=значение,свойство=значение,...])
   *
   * @param \PPHP\tools\patterns\database\LongObject $object
   */
  public function delete(\PPHP\tools\patterns\database\LongObject $object){
    $this->commands[] = 'delete(' . $this->toStringObject($object) . ')';
  }

  /**
   * Метод записывает имя команды и начальное состояние объекта, а так же использует текущую реакцию объекта для установки нового состояния.
   * Команда имеет сдедующую структуру:
   * recover(полноеИмяКлассаОбъекта[свойство=значение,свойство=значение,...])
   *
   * Массив ответа используется в следующем порядке:
   * 1.1. Exception - выбрасывается;
   * 1.2. array - устанавливается в качестве текущего состояния объекта.
   *
   * @param \PPHP\tools\patterns\database\LongObject $object
   * @throws mixed
   */
  public function recover(\PPHP\tools\patterns\database\LongObject &$object){
    $this->commands[] = 'recover(' . $this->toStringObject($object) . ')';
    $data = array_shift($this->returns);
    if(is_array($data) && count($data) != 0){
      $object->restoreFromMemento(new \PPHP\tools\patterns\memento\Memento($object, $data));
    }
    elseif($data instanceof \PPHP\tools\classes\standard\baseType\exceptions\Exception){
      throw $data;
    }
  }

  /**
   * Метод записывает имя команды и начальное состояние объекта, а так же использует текущую реакцию объекта для установки нового состояния.
   * Команда имеет сдедующую структуру:
   * recoverFinding(полноеИмяКлассаОбъекта[свойство=значение,свойство=значение,...],[свойство=искомоеЗначение,...])
   *
   * Массив ответа используется в следующем порядке:
   * 1. array - устанавливается в качестве текущего состояния объекта.
   * 2.1. Exception - выбрасывается;
   * 2.2. mixed - возвращается в return.
   *
   * @param \PPHP\tools\patterns\database\LongObject $object
   * @param array $requiredProperties
   * @throws mixed Если в качестве возвращаемого значения указано исключение, то оно будет выброшено из метода.
   * @return mixed|void
   */
  public function recoverFinding(\PPHP\tools\patterns\database\LongObject &$object, array $requiredProperties){
    foreach($requiredProperties as $k => &$v){
      $v = $k . '=' . (($v instanceof \PPHP\tools\patterns\database\LongObject)? $v->getLinkOID() : (($v instanceof \PPHP\tools\patterns\database\associations\LongAssociation)? 'LongAssociation': $v));
    }
    $this->commands[] = 'recoverFinding(' . $this->toStringObject($object) . ',[' . implode(',', $requiredProperties) . '])';
    $data = array_shift($this->returns);
    if(count($data) != 0){
      $object->restoreFromMemento(new \PPHP\tools\patterns\memento\Memento($object, $data));
    }
    $data = array_shift($this->returns);
    if($data instanceof \PPHP\tools\classes\standard\baseType\exceptions\Exception){
      throw $data;
    }
    else{
      return $data;
    }
  }

  /**
   * Метод записывает имя команды и начальное состояние объекта, и возвращает текущую реакцию объекта как результат работы метода.
   * Команда имеет сдедующую структуру:
   * recoverGroupFinding(имяИскомогоКласса,[свойство=искомоеЗначение,...])
   *
   * Массив ответа используется в следующем порядке:
   * 1.1. Exception - выбрасывается;
   * 1.2. mixed - возвращается в return.
   *
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass
   * @param array $requiredProperties
   * @throws mixed
   * @return \SplObjectStorage
   */
  public function recoverGroupFinding(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass, array $requiredProperties){
    foreach($requiredProperties as $k => &$v){
      $v = $k . '=' . (($v instanceof \PPHP\tools\patterns\database\LongObject)? $v->getLinkOID() : (($v instanceof \PPHP\tools\patterns\database\associations\LongAssociation)? 'LongAssociation': $v));
    }
    $this->commands[] = 'recoverGroupFinding(' . $reflectionClass->getName() . ',[' . implode(',', $requiredProperties) . '])';
    $data = array_shift($this->returns);
    if($data instanceof \PPHP\tools\classes\standard\baseType\exceptions\Exception){
      throw $data;
    }
    else{
      return $data;
    }
  }

  /**
   * Метод записывает имя команды, имя ассоциации, запрос, и заполняет ассоциацию данными из текущей реакции объекта.
   * Команда имеет сдедующую структуру:
   * recoverAssoc(имяКлассаАссоциации,selectЗапрос)
   *
   * Массив ответа используется в следующем порядке:
   * 1. array - множество сущностей, заполняющих множественную ассоциацию.
   *
   * @param \PPHP\tools\patterns\database\associations\LongAssociation $assoc
   */
  public function recoverAssoc(\PPHP\tools\patterns\database\associations\LongAssociation &$assoc){
    $this->commands[] = 'recoverAssoc(' . $assoc->getAssocClass()->getName() . ',' . $assoc->getSelectQuery()->interpretation() . ')';
    $data = array_shift($this->returns);

    if(!empty($data)){
      foreach($data as $v){
        $assoc->attach($v);
      }
    }
  }

  /**
   * Метод записывает имя команды и состояние объекта.
   * Команда имеет сдедующую структуру:
   * update(полноеИмяКлассаОбъекта[свойство=значение,свойство=значение,...])
   *
   * @param \PPHP\tools\patterns\database\LongObject $object
   */
  public function update(\PPHP\tools\patterns\database\LongObject $object){
    $this->commands[] = 'update(' . $this->toStringObject($object) . ')';
  }
}
