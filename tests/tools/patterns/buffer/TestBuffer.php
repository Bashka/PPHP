<?php
namespace PPHP\tests\tools\patterns\buffer;

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
    return $this->getData($key, $arguments);
  }
}
