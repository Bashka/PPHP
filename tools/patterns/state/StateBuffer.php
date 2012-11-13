<?php
namespace PPHP\tools\patterns\state;
use PPHP\tools\patterns\buffer\MapBuffer as MapBuffer;

/**
 * Класс является основой для буфера состояний объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\state
 */
abstract class StateBuffer extends MapBuffer{
  /**
   * Метод возвращает объект состояния по его имени.
   * @param string $stateName Имя состояния.
   * @param StatesContext $context Контекст.
   * @param array $links Открытые для состояния свойства контекста.
   * @return mixed
   */
  public function getState($stateName, StatesContext $context, array $links){
    return $this->getData($stateName, ['context' => $context, 'links' => $links]);
  }
}
