<?php
namespace PPHP\tools\classes\standard\storage\cache;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс реализует механизм хранения и поиска объектов типа LongObject в кэше.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\cache
 */
class Synchronizer implements Singleton{
  use TSingleton;

  /**
   * Метод добавляет состояние объекта в кэш.
   * @param string $className Имя класса объекта.
   * @param integer $OID Идентификатор объекта.
   * @param array $state Состояние объекта.
   */
  public function add($className, $OID, array $state){
    InvalidArgumentException::verifyType($className, 'S');
    InvalidArgumentException::verifyType($OID, 'i');
    $cache = Cache::getInstance();
    // Добавление объекта в кучу
    $cache->set('Synchronizer_Pile_' . $className . ':' . $OID, $state);
    // Добавление индекса на объект
    $classIndex = $cache->get('Synchronizer_Classes_' . $className);
    if(is_null($classIndex)){
      $classIndex = [];
    }
    if(array_search($OID, $classIndex) === false){
      $classIndex[] = $OID;
    }
    $cache->set('Synchronizer_Classes_' . $className, $classIndex);
  }

  /**
   * Метод возвращает состояние объекта из кэша по его идентификатору.
   * @param string $className Имя класса объекта.
   * @param integer $OID Идентификатор объекта.
   * @return null|array Состояние объекта или null если объект не найден.
   */
  public function get($className, $OID){
    InvalidArgumentException::verifyType($className, 'S');
    InvalidArgumentException::verifyType($OID, 'i');

    return Cache::getInstance()->get('Synchronizer_Pile_' . $className . ':' . $OID);
  }

  /**
   * Метод удаляет состояние объекта по его идентификатору.
   * @param string $className Имя класса объекта.
   * @param integer $OID Идентификатор объекта.
   */
  public function remove($className, $OID){
    InvalidArgumentException::verifyType($className, 'S');
    InvalidArgumentException::verifyType($OID, 'i');
    $cache = Cache::getInstance();
    $index = 'Synchronizer_Pile_' . $className . ':' . $OID;
    if(!is_null($cache->get($index))){
      // Удаление из кучи
      $cache->remove($index);
    }
    // Удаление из идекса
    if(!is_null($classIndex = $cache->get('Synchronizer_Classes_' . $className))){
      if(($key = array_search($OID, $classIndex)) !== false){
        unset($classIndex[$key]);
        $cache->set('Synchronizer_Classes_' . $className, $classIndex);
      }
    }
  }

  /**
   * Метод выполняет поиск объектов в кэше согласно некоторому условию.
   * @param string $className Имя класса объекта.
   * @param array $conditions Ассоциативный массив, определяющий условие отбора. Массив имеет следующую структуру: [[имяСвойства, оператор, значение], ...].
   * @return array Массив найденных состояний объектов. Ключами массива служат идентификатору соответствующих объектов. Массив пуст, если ни одного объекта не найдено по данному условию.
   */
  public function find($className, array $conditions){
    InvalidArgumentException::verifyType($className, 'S');
    $classIndex = Cache::getInstance()->get('Synchronizer_Classes_' . $className);
    $result = [];
    if(!is_null($classIndex)){
      foreach($classIndex as $OID){
        $state = $this->get($className, $OID);
        $flag = true;
        foreach($conditions as $condition){
          if(isset($state[$condition[0]])){
            switch($condition[1]){
              case '=':
                $flag *= ($state[$condition[0]] == $condition[2])? 1 : 0;
                break;
              case '!=':
                $flag *= ($state[$condition[0]] != $condition[2])? 1 : 0;
                break;
              case '>':
                $flag *= ($state[$condition[0]] > $condition[2])? 1 : 0;
                break;
              case '<':
                $flag *= ($state[$condition[0]] < $condition[2])? 1 : 0;
                break;
              case '>=':
                $flag *= ($state[$condition[0]] >= $condition[2])? 1 : 0;
                break;
              case '<=':
                $flag *= ($state[$condition[0]] <= $condition[2])? 1 : 0;
                break;
            }
          }
          else{
            $flag *= 0;
          }
        }
        if($flag){
          $result[$OID] = $state;
        }
      }
    }

    return $result;
  }
}