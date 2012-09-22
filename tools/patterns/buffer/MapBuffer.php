<?php
namespace PPHP\tools\patterns\buffer;

/**
 * Класс является основой для буферезующих декораторов.
 *
 * Для полной реализации класса достаточно реализовать его метод getFromSource, который должен возвращать буферизируемые данные по их имени и/или дополнительным аргументам.
 */
abstract class MapBuffer{
  /**
   * @var array Буфер данных.
   */
  private $mapBuffer = [];
  /**
   * @var array Специализированный буфер, отвечающий за хронологический учет буферизации.
   */
  private $indexBuffer = [];
  /**
   * @var int Максимальный размер буфера.
   */
  private $maxSizeBuffer;

  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * @abstract
   * @param string $key
   * @param array|null $arguments
   * @return mixed
   */
  protected abstract function getFromSource($key, array $arguments = null);

  /**
   * Метод возвращает данные, связанные с конкретным ключем из буфера.
   * Если данных нет в буфере, то они запрашиваются из первоисточника.
   * @param string $key
   * @param array|null $arguments
   * @return mixed
   */
  protected function getDate($key, array $arguments = null){
    // Обработка при отключенной буферизации
    if($this->maxSizeBuffer == 0){
      return $this->getFromSource($key, $arguments);
    }
    // Обработка с буферизацией
    if(!isset($this->mapBuffer[$key])){
      $this->mapBuffer[$key] = $this->getFromSource($key, $arguments);
      // Обработка конечного буфера
      if($this->maxSizeBuffer >= 0){
        array_push($this->indexBuffer, $key);
        if(count($this->mapBuffer) > $this->maxSizeBuffer){
          unset($this->mapBuffer[array_shift($this->indexBuffer)]);
        }
      }
    }
    return $this->mapBuffer[$key];
  }

  /**
   * Констурктор устанавливает максимальный размер буфера.
   * @param int $maxSizeBuffer Максимальный размер буфера. Если указан ноль, то буферизация отключается. Если используется отрицательное число, то буфер считается безконечным.
   */
  public function __construct($maxSizeBuffer = 50){
    $this->maxSizeBuffer = $maxSizeBuffer;
  }

  /**
   * Метод возвращает текущий максимальный размер буфера.
   * @return int Текущий максимальный размер буфера.
   */
  public function getMaxSizeBuffer(){
    return $this->maxSizeBuffer;
  }

  /**
   * Метод устанавливает максимальный размер буфера.
   * @param int $maxSizeBuffer Новый максимальный размер буфера.
   */
  public function setMaxSizeBuffer($maxSizeBuffer){
    $this->maxSizeBuffer = $maxSizeBuffer;
  }

  /**
   * Метод возвращает текущий размер буфера.
   * @return int Текущий размер буфера.
   */
  public function getSizeBuffer(){
    return count($this->mapBuffer);
  }
}
