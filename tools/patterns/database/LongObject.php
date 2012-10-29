<?php
namespace PPHP\tools\patterns\database;

/**
 * Корневой родительский класс для всех энергонезависимых объектов.
 *
 * Дочерние классы могут быть сохранены в постоянное хранилище и востановлены из него по требованию.
 * Те свойства объекта, которые могут быть сохранены помечаются метаданными NameFieldTable с именем поля, хранящим данное свойство в таблице.
 * Класс так же должен включать следуюшие метаданные: NameTable - имя таблицы, хранящей экземпляры данного класса; KeyTable - имя поля таблицы, хранящего первичный ключ таблицы.
 * Поле, содержащее первичный ключ объекта не следует помечать метаданными NameFieldTable.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database
 * @see PPHP\tools\patterns\database\identification\OID
 * @see PPHP\tools\patterns\metadata
 * @see PPHP\tools\patterns\memento
 */
abstract class LongObject implements \PPHP\tools\patterns\metadata\reflection\Reflect, \PPHP\tools\patterns\database\identification\OID, \PPHP\tools\patterns\memento\Originator{
  use \PPHP\tools\patterns\metadata\reflection\TReflect;
  use \PPHP\tools\patterns\memento\TOriginator;
  use \PPHP\tools\patterns\database\identification\TOID;
}
