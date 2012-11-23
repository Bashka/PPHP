<?php
namespace PPHP\model\modules;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
set_exception_handler(function($exc){
  $viewProvider = \PPHP\services\view\ViewProvider::getInstance();
  $send = new \stdClass();
  $send->exception = $exc;
  $viewProvider->sendMessage($send);
  exit(1);
});

/**
 * Класс является центральным контроллером системы и отвечает за вызов и передачу модулю сообщений вида, а так же за возврат ему ответа модуля.
 */
class CentralController{
  /**
   * Метод возвращает контроллер указанного конкретного модуля.
   * @static
   * @param string $moduleName Имя зарпашиваемого модуля.
   * @throws \PPHP\services\modules\ModuleNotFoundException Выбрасывается в случае, если требуемого модуля не существует в системе.
   * @return \PPHP\model\classes\ModuleController Контроллер целевого модуля.
   */
  public static function getControllerModule($moduleName){
    return \PPHP\services\modules\ModulesRouter::getInstance()->getController($moduleName);
  }

  /**
   * Данный метод отвечает за передачу модулю сообщения от слоя представления и возврат ответа.
   * @static
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если требуемый модуль не зарегистрирован в системе.
   */
  public static function main(){
    $viewProvider = \PPHP\services\view\ViewProvider::getInstance();
    $viewMessage = $viewProvider->getMessage();
    $module = $viewMessage['module'];
    $method = $viewMessage['active'];
    try{
      $controller = self::getControllerModule($module);
    }
    catch(\PPHP\services\modules\ModuleNotFoundException $exc){
      $send = new \stdClass();
      $send->exception = $exc;
      $viewProvider->sendMessage($send);
      exit(1);
    }

    $send = new \stdClass();
    if(!method_exists($controller, $method)){
      $send->exception = new \PPHP\tools\classes\standard\baseType\exceptions\LogicException('Запрашиваемый интерфейс '.$method.' модуля '.$module.' отсутствует.');
    }
    else{
      // Проверка прав доступа к методу модуля
      if(AccessManager::getInstance()->isResolved($module, $method)){
        try{
          // Верификация данных
          if(isset($viewMessage['message'])){
            VerifierData::verifyArgs($controller->getReflectionMethod($method), $viewMessage['message']);
          }
          else{
            $viewMessage['message'] = [];
          }

          $send->answer = call_user_func_array([$controller, $method], $viewMessage['message']);
        }
        catch(\Exception $e){
          \PPHP\services\log\LogManager::getInstance()->setMessage(\PPHP\services\log\Message::createError($e->getMessage(), $e));
          $send->exception = $e;
        }
      }
      else{
        $send->exception = new AccessException('Доступ запрещен.');
      }
    }
    $viewProvider->sendMessage($send);
  }
}

CentralController::main();