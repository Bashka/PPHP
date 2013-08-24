<?php
namespace PPHP\services\log;

use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter;
use PPHP\tools\classes\standard\storage\cache\Cache;
use PPHP\tools\classes\standard\storage\cache\CacheAdapter;
use PPHP\tools\patterns\singleton as singleton;

/**
 * Класс служит для журналирования сообщений системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\log
 */
class LogManager implements singleton\Singleton{
  use singleton\TSingleton;

  /**
   * Журнализация ошибок.
   */
  const ERROR = 'Error';

  /**
   * Журнализация ошибок и предупреждений.
   */
  const WARNING = 'Warning';

  /**
   * Журнализация ошибок, предупреждений и уведомлений.
   */
  const NOTICE = 'Notice';

  /**
   * Журнализация ошибок, предупреждений, уведомлений и информационных сообщений.
   */
  const INFO = 'Info';

  /**
   * Тип журнализации.
   * @var string
   */
  protected $type;

  /**
   * Поток ввода в журнал.
   * @var BlockingFileWriter
   */
  protected $writer;

  /**
   * @var Configurator
   */
  protected $conf;

  /**
   * @var CacheAdapter
   */
  protected $cache;

  /**
   * @throws NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации или отсутствия необходимых данных для инициализации службы.
   */
  private function __construct(){
    try{
      $this->cache = Cache::getInstance();
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
    if(!isset($this->cache->LogManager_Type)){
      try{
        $this->conf = Configurator::getInstance();
      }
      catch(NotFoundDataException $e){
        throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
      }
      if(!isset($this->conf->Log_Type)){
        throw new NotFoundDataException('Недостаточно данных для инициализации службы. Отсутствует обязательное свойство [Log::Type].');
      }
      try{
        $this->type = $this->conf->Log_Type;
      }
      catch(NotFoundDataException $e){
        throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
      }
      $this->cache->LogManager_Type = $this->type;
    }
    else{
      $this->type = $this->cache->LogManager_Type;
    }
  }

  /**
   * Метод устанавливает тип журнализируемой информации.
   * @param string $type Представление типа журнализируемой информации. Допустимыми значениями являются:
   * Info - ошибки, предупреждения и информационные сообщения;
   * Warning - Ошибки и предупреждения;
   * Error - Ошибки.
   * @throws InvalidArgumentException Выбрасывается в случае передачи неверного аргумента.
   * @throws NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return boolean true - если тип журнализации установлен успешно.
   */
  public function setType($type){
    InvalidArgumentException::verifyType($type, 'S');
    InvalidArgumentException::verifyVal($type, 's # ' . self::ERROR . '|' . self::WARNING . '|' . self::NOTICE . '|' . self::INFO);
    $this->type = $type;
    try{
      $this->conf->Log_Type = $type;
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
    $this->cache->LogManager_Type = $type;

    return true;
  }

  /**
   * @return string
   */
  public function getType(){
    return $this->type;
  }

  /**
   * Метод возвращает поток ввода в журнал.
   * @return BlockingFileWriter Поток ввода в журнал.
   */
  protected function getLog(){
    if(empty($this->writer)){
      $this->writer = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/services/log/log.txt');
      $lengthWriter = $this->writer->getSize();
      $this->writer = $this->writer->getWriter();
      $this->writer->setPosition($lengthWriter);
    }

    return $this->writer;
  }

  /**
   * Метод добавляет сообщение в журнал.
   * @param Message $message Добавляемое сообщение.
   * @return boolean true - если сообщение успешно записано, иначе - false.
   */
  public function setMessage(Message $message){
    $messType = $message->getType();
    if($this->type == self::ERROR && $messType != self::ERROR){
      return false;
    }
    elseif($this->type == self::WARNING && ($messType != self::ERROR || $messType != self::WARNING)){
      return false;
    }
    elseif($this->type == self::NOTICE && $messType == self::INFO){
      return false;
    }
    $this->getLog()->write($message->interpretation());

    return true;
  }
}
