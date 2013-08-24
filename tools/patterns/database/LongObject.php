<?php
namespace PPHP\tools\patterns\database;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter\RestorableAdapter;
use PPHP\tools\patterns\interpreter\TRestorable;
use PPHP\tools\patterns\memento as memento;
use PPHP\tools\patterns\metadata\reflection as reflection;

// @todo: Пересмотреть документацию класса для приведения к стандарту.
/**
 * Корневой родительский класс для всех энергонезависимых объектов.
 * Дочерние классы могут быть сохранены в постоянное хранилище и востановлены из него по требованию.
 * Те свойства объекта, которые могут быть сохранены помечаются метаданными NameFieldTable с именем поля, хранящим данное свойство в таблице.
 * Класс так же должен включать следуюшие метаданные: NameTable - имя таблицы, хранящей экземпляры данного класса; KeyTable - имя поля таблицы, хранящего первичный ключ таблицы.
 * Поле, содержащее первичный ключ объекта не следует помечать метаданными NameFieldTable.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database
 */
abstract class LongObject extends RestorableAdapter implements reflection\Reflect, identification\OID, memento\Originator{
  use reflection\TReflect;
  use memento\TOriginator;
  use identification\TOID;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['\$([A-Z\/a-z_]+):([0-9]+)'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    /**
     * @var string $m Имя класса восстанавливаемого объекта, в котором разделителем пакетом является символ косой черты.
     */
    $m = parent::reestablish($string);
    /**
     * @var LongObject $o Имя класса восстанавливаемого объекта.
     */
    $o = str_replace('/', '\\', $m[1]);

    return $o::getProxy((int) $m[2]);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если объект не идентифицирован.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    if(!$this->isOID()){
      throw new exceptions\NotFoundDataException('Объект не идентифицирован.');
    }

    return '$/' . str_replace('\\', '/', get_class($this)) . ':' . $this->getOID();
  }
}
