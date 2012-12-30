<?php
namespace PPHP\tests\tools\classes\special\storage\relationTable;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

class TestField extends \PPHP\tools\classes\special\storage\relationTable\Field{
  /**
   * Метод верифицирует данные в соответствии с типом.
   * @param mixed $data Верифицируемые данные.
   * @return boolean true - если данные не содержат отклонений от требований стандарта, иначе - false.
   */
  public function verify($data = null){
    return true;
  }

  /**
   * Метод приводить данные к требуемому для записи формату.
   * @param string $data Сериализуемые данные.
   * @return string Подготовленные для записи данные.
   */
  public function serializeData($data = null){
    return (string) $data;
  }

  /**
   * Метод востанавливает данные из сериализованной ранее им строки.
   * @param string $serializeData Сериализованные ранее данным классом данные.
   * @return null|mixed Востановленные данные.
   */
  public function unserializeData($serializeData){
    return trim((string) $serializeData);
  }

  /**
   * Метод возвращает длину данных после приведения.
   * @return integer Длина данных для записи.
   */
  public function getSizeData(){
    return 1;
  }
}
