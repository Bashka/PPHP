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
   * Журнализация ошибок, предупреждений и информационных сообщений.
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

  private function __construct(){
    if(empty($this->conf)){
      $this->conf = \PPHP\services\configuration\Configurator::getInstance();
    }
    if(empty($this->type)){
      $this->type = $this->conf->get('Log', 'Type');
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
    if(!is_string($type)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $type);
    }
    if($type != self::ERROR && $type != self::WARNING && $type != self::INFO){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Недопустимое значение аргумента.');
    }

    $this->type = $type;
    $this->conf->set('Log', 'Type', $type);
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
      $this->writer = $this->writer->getWriter();
    }
    return $this->writer;
  }

  /**
   * Метод добавляет сообщение в журнал.
   * @param Message $message Добавляемое сообщение.
   * @return boolean true - если сообщение успешно записано, иначе - false.
   */
  public function setMessage(Message $message){
    if($this->type == self::WARNING && $message->getType() == self::INFO){
      return false;
    }
    if($this->type == self::ERROR && $message->getType() != self::ERROR){
      return false;
    }
    $this->getLog()->write($message->serialize());
    return true;
  }
}
