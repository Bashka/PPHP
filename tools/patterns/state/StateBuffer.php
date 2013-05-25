<?php
namespace PPHP\tools\patterns\state;
use PPHP\tools\patterns\buffer\MapBuffer as MapBuffer;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс является основой для буфера состояний объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
abstract class StateBuffer extends MapBuffer{
  /**
   * Метод возвращает объект состояния по его имени.
   *
   * @param string $stateName Имя состояния.
   * @param StatesContext $context Контекст.
   * @param mixed[] $links Открытые для состояния свойства контекста.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия состояния с указанным именем.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return State Объект состояния, связанный с данным именем.
   */
  public function getState($stateName, StatesContext $context, array $links){
    try{
      return $this->getData($stateName, ['context' => $context, 'links' => $links]);
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
  }
}
