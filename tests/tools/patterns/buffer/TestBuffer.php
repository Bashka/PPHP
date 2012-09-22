<?php
namespace PPHP\tests\tools\patterns\buffer;
spl_autoload_register(function($className){
  $root = 'C:/WebServers/home/dic/www';
  require_once $root . '/' . str_replace('\\', '/', $className) . '.php';
});

class TestBuffer extends \PPHP\tools\patterns\buffer\MapBuffer{
  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * @param string $key
   * @param array|null $arguments
   * @return mixed
   */
  protected function getFromSource($key, array $arguments = null){
    return $key;
  }

  public function get($key, array $arguments = null){
    return $this->getDate($key, $arguments);
  }
}
