<?php
namespace PPHP\services\formatting\localisation;

use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\patterns\database\LongObject;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;
use PPHP\tools\patterns\metadata\reflection\ReflectionProperty;
use PPHP\tools\patterns\singleton as singleton;

/**
 * Класс позволяет локализовать сообщения в соответствии с файлами локализации.
 * Файл локализации толжен находится в том же каталоге, что и локализуемый класс, начинаться с того же имени, а так же иметь постфикс в соответствии с локализацией, так для английской локализации постфикс должен иметь вид _en
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\formatting\localisation
 */
class LocalisationManager implements singleton\Singleton{
  use singleton\TSingleton;

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
  const bufferSize = 50;

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

  /**
   * Метод возвращает маски всех возможных языков локализации.
   * @static
   * @return string[] Массив масок всех возможных языков локализации.
   */
  public static function getLanguages(){
    return [self::ENGLISH, self::RUSSIA];
  }

  /**
   * Метод возвращает локализацию по умолчанию.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   * @return string Локализация по умолчанию.
   */
  public static function getDefaultLanguage(){
    try{
      return Configurator::getInstance()->get('Localisation', 'DefaultLanguage');
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
  }

  private function __construct(){
    $this->buffer = new LocaliseBuffer(self::bufferSize);
  }

  /**
   * Метод устанавливает текущую локализацию
   * @param string $localise Одна из констант класса, определяющая локализацию
   */
  public function setLocalise($localise){
    $this->currentLocalise = $localise;
  }

  /**
   * Метод локализует данное сообщение в соответствии с файлом локализации данного класса.
   * @param ReflectionClass $class Класс-владелец файла локализации.
   * @param string $message Локализуемое сообщение.
   * @throws InvalidArgumentException Выбрасывается в случае, если передано значение аргумента неожиданного типа.
   * @return string Локализованное сообщение или входящее сообщение, если для него не определены данные для локализации или файла локализации не найдено.
   */
  public function localiseMessage(ReflectionClass $class, $message){
    InvalidArgumentException::verifyType($message, 'S');
    $addressLocaliseFile = $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $class->getName()) . '_' . $this->currentLocalise . '.ini';
    try{
      $localiseData = $this->buffer->getData($addressLocaliseFile);
    }
    catch(NotFoundDataException $exc){
      return $message;
    }
    catch(InvalidArgumentException $exc){
      return $message;
    }
    if(isset($localiseData[$message])){
      return $localiseData[$message];
    }

    return $message;
  }

  /**
   * Метод локализует имя класса
   * @param ReflectionClass $class Локализуемый класс
   * @throws InvalidArgumentException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return string Локализованное имя класса или оригинальное имя класса, если для него не определены данные для локализации
   */
  public function localiseClass(ReflectionClass $class){
    $className = $class->getName();
    $localiseMessage = substr($className, strrpos($className, '\\') + 1);

    return $this->localiseMessage($class, $localiseMessage);
  }

  /**
   * Метод локализует свойство класса
   * @param ReflectionClass $class Класс, членом которого является локализуемое свойство
   * @param ReflectionProperty $property Локализуемое свойство
   * @throws InvalidArgumentException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return string Локализованное имя свойства класса или оригинальное имя свойства класса, если для него не определены данные для локализации
   */
  public function localiseProperty(ReflectionClass $class, ReflectionProperty $property){
    return $this->localiseMessage($class, $property->getName());
  }

  /**
   * Метод локализует переданную сущность и возвращает ее локализованное представление.
   * В локализованном представлении объекта имена его свойств локализуются, а так же локализуются их значения типа string.
   * В качестве свойства localiseName устанавливается локализованное имя сущности.
   * @param LongObject $essence Локализуемая сущность.
   * @return \stdClass Локализованный объект.
   */
  public function localiseEssence(LongObject $essence){
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