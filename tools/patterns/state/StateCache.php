<?php
namespace PPHP\tools\patterns\state;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\cache\Cache;

/**
 * Кэш-фабрика состояний.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
abstract class StateCache extends Cache{
  /**
   * Метод возвращает объект состояния по его имени.
   * @param string $stateName Имя состояния.
   * @param StatesContext $context Контекст.
   * @param mixed[] $links Открытые для состояния свойства контекста.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия состояния с указанным именем.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
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
