<?php
namespace PPHP\tools\patterns\database;
use \PPHP\tools\patterns\metadata\reflection as reflection;
use \PPHP\tools\patterns\memento as memento;

/**
 * Корневой родительский класс для всех энергонезависимых объектов.
 *
 * Дочерние классы могут быть сохранены в постоянное хранилище и востановлены из него по требованию.
 * Те свойства объекта, которые могут быть сохранены помечаются метаданными NameFieldTable с именем поля, хранящим данное свойство в таблице.
 * Класс так же должен включать следуюшие метаданные: NameTable - имя таблицы, хранящей экземпляры данного класса; KeyTable - имя поля таблицы, хранящего первичный ключ таблицы.
 * Поле, содержащее первичный ключ объекта не следует помечать метаданными NameFieldTable.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database
 */
abstract class LongObject implements reflection\Reflect, identification\OID, memento\Originator{
  use reflection\TReflect;
  use memento\TOriginator;
  use identification\TOID;
}
