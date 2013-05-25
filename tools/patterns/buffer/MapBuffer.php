<?php
namespace PPHP\tools\patterns\buffer;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс является основой для буферезующих декораторов.
 * Использование данного класса позволяет реализовать буфер уровня сеанса. Буфер может иметь ограничение на число буферизуемых данных, либо хранить неограниченное их число.
 * Для полной реализации класса достаточно реализовать его метод getFromSource, который должен возвращать буферизируемые данные по их имени и/или дополнительным аргументам.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\buffer
 */
abstract class MapBuffer{
  /**
   * Буфер данных.
   * @var mixed[]
   */
  private $mapBuffer = [];
  /**
   * Индекс, отвечающий за хронологический учет буферизации для целей уничтожения устарелых данных в буфере.
   * @var mixed[]
   */
  private $indexBuffer = [];
  /**
   * Максимальный размер буфера. При значении 0, буферизация отключается, а при отрицательном значении, буферизация является неограниченной.
   * @var integer
   */
  private $maxSizeBuffer;

  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * Данный метод должен возвращать данные по их идентификатору. При нахождении данных они автоматически буферизуются и могут быть получены из буфера.
   * Если для нахождения или инициализации данных недостаточно идентификатора, может быть использован дополнительный аргумент.
   * @abstract
   *
   * @param string $key Идентификатор данных.
   * @param mixed[] $arguments [optional] Дополнительные аргументы, используемые для идентификации данных.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных из источника.
   * @return mixed Связанные с ключом данные.
   */
  protected abstract function getFromSource($key, array $arguments = null);

  /**
   * Метод возвращает данные, связанные с конкретным ключем.
   * Данный метод используется для получения данных по идентификационному ключу.
   * Если данные были буферизованы, они будут возвращены из буфера.
   * Если данных нет в буфере, то они запрашиваются из первоисточника с последующей их буферизацией.
   *
   * @param string $key Идентификатор данных.
   * @param mixed[] [optional] $arguments Дополнительные аргументы, используемые для идентификации данных.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных из источника.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return mixed Связанные с ключом данные.
   */
  public function getData($key, array $arguments = null){
    exceptions\InvalidArgumentException::verifyType($key, 'S');

    try{ // Обработка при отключенной буферизации
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
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
    return $this->mapBuffer[$key];
  }

  /**
   * Констурктор устанавливает максимальный размер буфера.
   * @param integer $maxSizeBuffer Максимальный размер буфера. Если указан 0, то буферизация отключается, а если используется отрицательное число, то буфер считается безконечным.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function __construct($maxSizeBuffer = 50){
    exceptions\InvalidArgumentException::verifyType($maxSizeBuffer, 'i');
    $this->maxSizeBuffer = $maxSizeBuffer;
  }

  /**
   * Метод возвращает текущий максимальный размер буфера.
   * @return integer Текущий максимальный размер буфера.
   */
  public function getMaxSizeBuffer(){
    return $this->maxSizeBuffer;
  }

  /**
   * Метод устанавливает максимальный размер буфера.
   * Данный метот позволяет изменить максимальный размер буфера.
   * Если указан 0, то буферизация отключается, а если используется отрицательное число, то буфер считается безконечным.
   * @param integer $maxSizeBuffer Новый максимальный размер буфера.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function setMaxSizeBuffer($maxSizeBuffer){
    exceptions\InvalidArgumentException::verifyType($maxSizeBuffer, 'i');
    $this->maxSizeBuffer = $maxSizeBuffer;
  }

  /**
   * Метод возвращает текущий размер буфера, то есть число буферизованных данных.
   * @return integer Текущий размер буфера.
   */
  public function getSizeBuffer(){
    return count($this->mapBuffer);
  }

  /**
   * Метод отчищает буфер.
   */
  public function clearBuffer(){
    $this->mapBuffer = [];
    $this->indexBuffer = [];
  }
}
