<?php
namespace PPHP\tools\patterns\buffer;

/**
 * Класс является основой для буферезующих декораторов.
 *
 * Использование данного класса позволяет реализовать буфер уровня сеанса. Буфер может иметь ограничение на число буферизуемых данных, либо хранить неограниченное их число.
 * Для полной реализации класса достаточно реализовать его метод getFromSource, который должен возвращать буферизируемые данные по их имени и/или дополнительным аргументам.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\buffer
 */
abstract class MapBuffer{
  /**
   * @var mixed[] Буфер данных.
   */
  private $mapBuffer = [];
  /**
   * @var mixed[] Индекс, отвечающий за хронологический учет буферизации для целей уничтожения устарелых данных в буфере.
   */
  private $indexBuffer = [];
  /**
   * @var int Максимальный размер буфера. При значении 0, буферизация отключается, а при отрицательном значении, буферизация является неограниченной.
   */
  private $maxSizeBuffer;

  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   *
   * Данный метод должен возвращать данные по их идентификатору. При нахождении данных они автоматически буферизуются и могут быть получены из буфера.
   * Если для нахождения или инициализации данных недостаточно идентификатора, может быть использован дополнительный аргумент.
   * @abstract
   * @param string $key Идентификатор данных.
   * @param mixed[]|null $arguments Дополнительные аргументы, используемые для идентификации данных.
   * @return mixed Связанные с ключом данные.
   */
  protected abstract function getFromSource($key, array $arguments = null);

  /**
   * Метод возвращает данные, связанные с конкретным ключем.
   *
   * Данный метод используется для получения данных по идентификационному ключу.
   * Если данные были буферизованы, они будут возвращены из буфера.
   * Если данных нет в буфере, то они запрашиваются из первоисточника с последующей их буферизацией.
   * @param string $key Идентификатор данных.
   * @param mixed[]|null $arguments Дополнительные аргументы, используемые для идентификации данных.
   * @return mixed Связанные с ключом данные.
   */
  protected function getData($key, array $arguments = null){
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
   * @param int $maxSizeBuffer Максимальный размер буфера. Если указан 0, то буферизация отключается, а если используется отрицательное число, то буфер считается безконечным.
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
   *
   * Данный метот позволяет изменить максимальный размер буфера.
   * Если указан 0, то буферизация отключается, а если используется отрицательное число, то буфер считается безконечным.
   * @param int $maxSizeBuffer Новый максимальный размер буфера.
   */
  public function setMaxSizeBuffer($maxSizeBuffer){
    $this->maxSizeBuffer = $maxSizeBuffer;
  }

  /**
   * Метод возвращает текущий размер буфера, то есть число буферизованных данных.
   * @return int Текущий размер буфера.
   */
  public function getSizeBuffer(){
    return count($this->mapBuffer);
  }
}
