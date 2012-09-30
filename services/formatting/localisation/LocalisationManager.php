<?php
namespace PPHP\services\formatting\localisation;

/**
 * Класс позволяет локализовать сообщения в соответствии с файлами локализации.
 * Файл локализации толжен находится в том же каталоге, что и локализуемый класс, начинаться с того же имени, а так же иметь постфикс в соответствии с локализацией, так для английской локализации постфикс должен иметь вид _en
 */
class LocalisationManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Английская локализация
   */
  const ENGLISH = 'en';
  /**
   * Русская локализация
   */
  const RUSSIA = 'ru';
  /**
   * Максимальный размер буфера
   */
  const bufferSize = 10;
  /**
   * Буфер данных для локализации
   * @var LocaliseBuffer
   */
  private $buffer;

  /**
   * Текущая локализация
   * @var string
   */
  private $currentLocalise;

  private function __construct(){
    $this->buffer = new LocaliseBuffer(self::bufferSize);
    $this->currentLocalise = self::ENGLISH;
  }

  /**
   * Метод устанавливает текущую локализацию
   * @param string $localise Одна из констант класса, определяющая локализацию
   */
  public function setLocalise($localise){
    $this->currentLocalise = $localise;
  }

  /**
   * Метод локализует данное сообщение в соответствии с файлом локализации данного класса
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $class Класс-владелец файла локализации
   * @param string $message Локализуемое сообщение
   * @return string Локализованное сообщение или входящее сообщение, если для него не определены данные для локализации
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если передано значение аргумента неожиданного типа или требуемого файла локализации не существует
   */
  public function localiseMessage(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $class, $message){
    if(!is_string($message)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $message);
    }
    $addressLocaliseFile = $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $class->getName()) . '_' . $this->currentLocalise . '.ini';
    $localiseData = $this->buffer->getLocaliseData($addressLocaliseFile);
    if(isset($localiseData[$message])){
      return $localiseData[$message];
    }
    return $message;
  }

  /**
   * Метод локализует имя класса
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $class Локализуемый класс
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return string Локализованное имя класса или оригинальное имя класса, если для него не определены данные для локализации
   */
  public function localiseClass(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $class){
    $className = $class->getName();
    $localiseMessage = substr($className, strrpos($className, '\\') + 1);
    return $this->localiseMessage($class, $localiseMessage);
  }

  /**
   * Метод локализует свойство класса
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $class Класс, членом которого является локализуемое свойство
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $property Локализуемое свойство
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return string Локализованное имя свойства класса или оригинальное имя свойства класса, если для него не определены данные для локализации
   */
  public function localiseProperty(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $class, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $property){
    return $this->localiseMessage($class, $property->getName());
  }

  /**
   * Метод локализует переданную сущность и возвращает ее локализованное представление.
   * В локализованном представлении объекта имена его свойств локализуются, а так же локализуются их значения типа string.
   * В качестве свойства localiseName устанавливается локализованное имя сущности.
   * @param \PPHP\tools\patterns\database\LongObject $essence Локализуемая сущность.
   * @return \stdClass Локализованный объект.
   */
  public function localiseEssence(\PPHP\tools\patterns\database\LongObject $essence){
    $state = $essence->createMemento()->getState($essence);
    $localiseObject = new \stdClass();
    foreach($state as $k => $v){
      $propName = $this->localiseProperty($essence->getReflectionClass(), $essence->getReflectionProperty($k));
      $localiseObject->$propName = (is_string($v))? $this->localiseMessage($essence->getReflectionClass(), $v) : $v;
    }
    $localiseObject->localiseName = $this->localiseClass($essence->getReflectionClass());

    return $localiseObject;
  }
}