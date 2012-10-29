<?php
namespace PPHP\model\modules\utilities;

/**
 * Выбрасывается в случае, если требуемой для работы компонента утилиты не существует.
 */
class UtilityNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
  public function __construct($utilityName, $code = 0, \Exception $previous = null){
    parent::__construct('Требуемой для работы утилиты '.$utilityName.' не найдено.', $code, $previous);
  }

}
