<?php
namespace PPHP\services\view;

/**
 * Выбрасывается в случае, если требуемого экрана не найдено.
 */
class ScreenNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
  public function __construct($moduleName = '', $screenName = '', $code = 0, Exception $previous = null){
    if(empty($moduleName) || empty($screenName)){
      parent::__construct($moduleName, $code, $previous);
    }
    else{
      parent::__construct('Требуемый экран '.$moduleName.'::'.$screenName.' не установлен.', $code, $previous);
    }
  }
}
