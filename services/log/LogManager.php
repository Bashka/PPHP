<?php
namespace PPHP\services\log;

class LogManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

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
   * @var \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter
   */
  protected $writer;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;
  /**
   * @var \PPHP\services\cache\CacheAdapter
   */
  protected $cache;

  private function __construct(){
    $this->cache = \PPHP\services\cache\CacheSystem::getInstance();
    if(!isset($this->cache->LogManager_Type)){
      $this->conf = \PPHP\services\configuration\Configurator::getInstance();
      $this->type = $this->conf->Log_Type;
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
   * @return boolean true - если тип журнализации установлен успешно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае передачи неверного аргумента.
   */
  public function setType($type){
    \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException::verifyType($type, 'S');
    \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException::verifyVal($type, 's # '.self::ERROR.'|'.self::WARNING.'|'.self::NOTICE.'|'.self::INFO);

    $this->type = $type;
    $this->conf->Log_Type = $type;
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
   * @return \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter
   */
  protected function getLog(){
    if(empty($this->writer)){
      $this->writer = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/services/log/log.txt');
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
