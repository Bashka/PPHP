<?php
namespace PPHP\services\view;

/**
 * Класс предоставляет доступ к виду и позволяет обмениваться с ним сообщениями.
 */
class ViewProvider implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод возвращает сообщение, полученное от вида.
   * @return mixed|null Содержимое сообщения или null - если сообщение не было передано.
   */
  public function getMessage(){
    return $_REQUEST;
  }

  /**
   * Метод передает сообщение виду.
   * @param mixed $message Содержимое сообщения.
   */
  public function sendMessage($message){
    echo json_encode($message);
  }
}