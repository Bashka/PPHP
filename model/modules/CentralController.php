<?php
namespace PPHP\model\modules;

  //@todo: Протестировать отлов Warning и Notice
// Автозагрузка классов
use PPHP\model\classes\ModuleController;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\log\LogManager;
use PPHP\services\log\Message;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\view\ViewProvider;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
ob_start();
// Обработка error
register_shutdown_function(function (){
  $error = error_get_last();
  // Обработка ошибки
  if($error){
    $buffer = ob_get_contents();
    ob_end_clean();
    header('HTTP/1.1 200 OK');
    $log = LogManager::getInstance();
    // Фатальные ошибки
    if($error['type'] == E_CORE_ERROR || $error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_USER_ERROR || $error['type'] == E_RECOVERABLE_ERROR || $error['type'] == E_COMPILE_ERROR){
      $log->setMessage(Message::createError($error['message'] . ' - ' . $error['file'] . ':' . $error['line']));
      // Обработка исключений
      $send = new \stdClass();
      $send->exception = new \stdClass();
      $send->exception->type = 1;
      if(strpos($error['message'], 'Uncaught exception') === 0){
        $className = strpos($error['message'], "'");
        $className = substr($error['message'], $className + 1, strpos($error['message'], "'", $className + 1) - $className - 1);
        $send->exception->class = $className;
      }
      else{
        $send->exception->class = '';
      }
      $send->exception->message = $error['message'];
      $send->exception->file = $error['file'];
      $send->exception->line = $error['line'];
      $send->exception->buffer = $buffer;
      $viewProvider = ViewProvider::getInstance();
      $viewProvider->sendMessage($send);
    }
  }
  else{
    ob_end_flush();
  }
});
// Обработка warning
set_error_handler(function ($code, $message, $file, $line){
  $log = LogManager::getInstance();
  $log->setMessage(Message::createWarning($message . ' - ' . $file . ':' . $line));
}, E_COMPILE_WARNING | E_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_CORE_WARNING);
// Обработка notice
set_error_handler(function ($code, $message, $file, $line){
  $log = LogManager::getInstance();
  $log->setMessage(Message::createNotice($message . ' - ' . $file . ':' . $line));
}, E_NOTICE | E_USER_NOTICE | E_STRICT);
/**
 * Класс является единой точной входа системы и отвечает за вызов и передачу модулю сообщений от слоя view, а так же за возврат ему ответа модуля.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules
 */
class CentralController{
  /**
   * Метод возвращает контроллер указанного конкретного модуля.
   * @static
   * @param string $moduleName Имя зарпашиваемого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае, если требуемого модуля не существует в системе.
   * @throws RuntimeException Выбрасывается в случае, если модуль не является конкретным.
   * @return ModuleController Контроллер целевого модуля.
   */
  public static function getControllerModule($moduleName){
    try{
      return (new ReflectionModule($moduleName))->getController();
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    catch(RuntimeException $e){
      throw $e;
    }
  }

  /**
   * Данный метод отвечает за передачу модулю сообщения от слоя представления и возврат ответа.
   * @static
   * @throws exceptions\ComponentClassException Выбрасывается в случае отсутствия запрашиваемого метода модуля.
   * @throws AccessException Выбрасывается в случае, если доступ к данному методу модуля запрещен.
   */
  public static function main(){
    $viewProvider = ViewProvider::getInstance();
    $viewMessage = $viewProvider->getMessage();
    $module = $viewMessage['module'];
    $method = $viewMessage['active'];
    $controller = self::getControllerModule($module);
    $send = new \stdClass();
    if(!method_exists($controller, $method) || $method == 'afterRun' || $method == 'beforeRun'){
      throw new exceptions\ComponentClassException('Запрашиваемый метод [' . $method . '] модуля [' . $module . '] отсутствует.');
    }
    else{
      // Проверка прав доступа к методу модуля
      if(AccessManager::getInstance()->isResolved($module, $method)){
        // Верификация данных
        if(isset($viewMessage['message'])){
          $viewMessage['message'] = json_decode($viewMessage['message']);
          VerifierData::verifyArgs($controller->getReflectionMethod($method), $viewMessage['message']);
        }
        else{
          $viewMessage['message'] = [];
        }
        $controller->afterRun();
        $send->answer = call_user_func_array([$controller, $method], $viewMessage['message']);
        $controller->beforeRun();
      }
      else{
        throw new AccessException('Доступ запрещен.');
      }
    }
    $viewProvider->sendMessage($send);
  }
}

CentralController::main();