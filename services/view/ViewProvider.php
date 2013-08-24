<?php
namespace PPHP\services\view;

use PPHP\tools\patterns\singleton as singleton;

/**
 * Класс предоставляет доступ к виду и позволяет обмениваться с ним сообщениями.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\view
 */
class ViewProvider implements singleton\Singleton{
  use singleton\TSingleton;

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