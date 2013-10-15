<?php
namespace PPHP\tools\patterns\cache;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, позволяющий хранить часто запрашиваемые у ресурса данные на период исполнения скрипта для повышения скорости доступа к ним.
 * Кэш может иметь ограничение на число хранящихся данных (объем), либо хранить неограниченное их число.
 * Если кэш ограниченный, то устаревшие данные будут автоматически удаляться из него.
 * Перевод кэша в состояние ограниченного, неограниченного или не кэширующего может осуществляться динамически в ходе его использования.
 * Кэш может быть отчищен в ходе его использования.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\cache
 */
abstract class Cache{
  /**
   * Признак отключения кэша. Данная константа может быть использована в методе setVolume для отключения кэширования.
   */
  const OFF = 0;

  /**
   * Признак неограниченного кэша. Данная константа может быть использована в методе setVolume для создания неограниченного кэша.
   */
  const NO_LIMITED = -1;

  /**
   * @var mixed[] Хранимые в кэше данные.
   */
  private $cachedData = [];

  /**
   * @var string[] Индекс, отвечающий за хронологический учет хранящихся в кэше данных для целей их уничтожения после устаревания.
   */
  private $ordinalIndex = [];

  /**
   * @var integer Объем ограниченного кэша. При значении self::OFF, кэширование отключается, а при значении self::NO_LIMITED, кэш является неограниченным.
   */
  private $volume = 0;

  /**
   * @var mixed|null Целевой ресурс кэша.
   */
  protected $resource;

  /**
   * Метод служит для запроса данных из ресурса в случае отсутствия их в кэше.
   * Метод должен возвращать данные по их идентификатору. При нахождении данных они автоматически кэшируются вызывающим методом (getData) и могут быть получены в будущем из кэша.
   * Если инициализированные данные необходимо дополнить, передается параметр $arguments.
   * @abstract
   * @param string $key Идентификатор данных.
   * @param mixed[] $arguments [optional] Дополнительные аргументы, используемые для дополнения идентифицированных данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных из ресурса.
   * @return mixed|null Связанные с идентификатором данные или null, если для данного идентификатора данных нет.
   */
  protected abstract function getFromSource($key, array $arguments = null);

  /**
   * Метод возвращает данные, связанные с конкретным идентификатором.
   * Если данные были записаны в кэш, они будут возвращены из него, иначе они запрашиваются из ресурса с помощью метода getFromSource, кэшируются и затем возвращаются.
   * Метод автоматически удаляет устаревшие данные в ограниченном кэше.
   * Если инициализированные данные необходимо дополнить, может быть использован дополнительный аргумент $arguments, он будет передан в метод getFromSource при запросе.
   * @param string $key Идентификатор данных.
   * @param mixed[] $arguments [optional] Дополнительные аргументы, используемые для дополнения идентифицированных данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных из ресурса.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return mixed Связанные с идентификатором данные.
   */
  public function getData($key, array $arguments = null){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    try{
      // Обход кэша при отключенном кэшировании
      if($this->volume == self::OFF){
        return $this->getFromSource($key, $arguments);
      }
      // Кэширование
      if(!array_key_exists($key, $this->cachedData)){
        $this->cachedData[$key] = $this->getFromSource($key, $arguments);
        // Поиск и удаление устаревших данных в ограниченном кэше
        if($this->volume != self::NO_LIMITED){
          array_push($this->ordinalIndex, $key);
          if($this->getDensity() > $this->volume){
            unset($this->cachedData[array_shift($this->ordinalIndex)]); // Удаление устаревших данных параллельно с удалением индекса этих данных
          }
        }
      }
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }

    return $this->cachedData[$key];
  }

  /**
   * Констурктор определяет объем кэша, отключает кэширование или делает кэш неограниченным.
   * @param integer $volume Объем кэша. При значении self::OFF, кэширование отключается, а при значении self::NO_LIMITED, кэш является неограниченным.
   * @param mixed $resource [optional] Целевой ресурс кэша.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function __construct($volume = 50, $resource = null){
    exceptions\InvalidArgumentException::verifyType($volume, 'i');
    $this->volume = $volume;
    $this->resource = $resource;
  }

  /**
   * Метод возвращает объем кэша или признак неограниченности/отключения кэша.
   * @return integer Объем кэша.
   */
  public function getVolume(){
    return $this->volume;
  }

  /**
   * Метод устанавливает объем ограниченного кэша, отключает кэширование или делает кэш неограниченным.
   * @param integer $volume Объем кэша. При значении self::OFF, кэширование отключается, а при значении self::NO_LIMITED, кэш является неограниченным.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function setVolume($volume){
    exceptions\InvalidArgumentException::verifyType($volume, 'i');
    $this->volume = $volume;
  }

  /**
   * Метод возвращает плотность кэша.
   * @return integer Текущая плотность кэша.
   */
  public function getDensity(){
    return count($this->cachedData);
  }

  /**
   * Метод отчищает кэш.
   */
  public function clear(){
    $this->cachedData = [];
    $this->ordinalIndex = [];
  }
}
