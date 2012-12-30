<?php
namespace PPHP\services\modules;

/**
 * Выбрасывается в случае, если требуемого модуля не найдено.
 */
class ModuleNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
  public function __construct($moduleName = '', $code = 0, Exception $previous = null){
    if(empty($moduleName)){
      parent::__construct($moduleName, $code, $previous);
    }
    else{
      parent::__construct('Требуемый модуль '.$moduleName.' не установлен.', $code, $previous);
    }
  }
}
