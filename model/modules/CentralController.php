<?php
namespace PPHP\model\modules;
$_SERVER['DOCUMENT_ROOT'] = 'C:/WebServers/home/Delphinum/www';
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

/**
 * Класс является центральным контроллером системы и отвечает за вызов и передачу модулю сообщений вида, а так же за возврат ему ответа модуля.
 */
class CentralController{
  /**
   * Роутер модулей.
   * @var \PPHP\services\modules\ModulesRouter
   */
  protected static $moduleRouter;

  /**
   * Данный ментод отвечает за передачу модулю сообщения и возврат ответа.
   * @static
   * @throws \PPHP\services\modules\ModuleNotFoundException Выбрасывается в случае, если требуемый модуль не зарегистрирован в системе.
   */
  public static function main(){
    $viewProvider = \PPHP\services\view\ViewProvider::getInstance();
    $viewMessage = $viewProvider->getMessage();
    self::$moduleRouter = \PPHP\services\modules\ModulesRouter::getInstance();
    if(!self::$moduleRouter->isModuleExists($viewMessage['module'])){
      $send = new \stdClass();
      $send->exception = new \PPHP\services\modules\ModuleNotFoundException('Требуемого модуля "' . $viewMessage['module'] . '" не существует.');
      $viewProvider->sendMessage($send);
      exit(1);
    }
    $controller = self::$moduleRouter->getController($viewMessage['module']);
    $controller = $controller::getInstance();

    $send = new \stdClass();

    $method = $viewMessage['active'];
    if(!method_exists($controller, $method)){
      $send->exception = new \PPHP\services\modules\ModuleNotFoundException('Запрашиваемый интерфейс '.$method.' модуля '.$viewMessage['module'].' отсутствует.');
    }
    else{
      // Проверка прав доступа к методу модуля
      if(AccessManager::getInstance()->isResolved($viewMessage['module'], $viewMessage['active'], self::$moduleRouter)){
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
          $send->exception = $e;
        }
      }
      else{
        $send->exception = new AccessException('Доступ запрещен.');
      }
    }
    $viewProvider->sendMessage($send);
  }

  /**
   * Метод позволяет зависимым модулям обращаться к контроллеру родительского модуля.
   * Чтобы организовать зависимость между модулями, необходимо добавить аннотацию ParentModule с именем родительского модуля в контроллер дочернего модуля.
   * @static
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $causingModule Отражение контроллера вызывающего модуля.
   * @param string $action Вызываемый метод контроллера родительского модуля.
   * @param mixed ... Параметры, передаваемые вызываемому методу.
   * @throws \PPHP\tools\patterns\metadata\EmptyMetadataException Выбрасывается в случае, если дочерним модулем не определена зависимость.
   * @return mixed Ответ вызываемого метода.
   */
  public static function sendParent(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $causingModule, $action){
    if(!$causingModule->isMetadataExists('ParentModule')){
      throw new \PPHP\tools\patterns\metadata\EmptyMetadataException('Информация о зависимости модуля отсутствует.');
    }
    $controllerParentModule = self::$moduleRouter->getController($causingModule->getMetadata('ParentModule'));
    $message = func_get_args();
    array_shift($message);
    array_shift($message);
    return call_user_func_array([$controllerParentModule::getInstance(), $action], $message);
  }
}

CentralController::main();