<?php
namespace PPHP\tools\patterns\database\persistent;

use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\patterns\database\identification as identification;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\interpreter\RestorableAdapter;
use PPHP\tools\patterns\memento as memento;
use PPHP\tools\patterns\metadata\reflection as reflection;

/**
 * Корневой родительский класс для всех персистентных объектов.
 * Дочерние классы могут быть сохранены в постоянное хранилище и востановлены из него по требованию при помощи пакета PPHP/tools/classes/standard/storage/database.
 * Дочерние классы должны быть аннотированы согласно инструкции, приведенной для пакета PPHP/tools/classes/standard/storage/database.
 * Дочерние классы должны реализовать методы getSavedState и setSavedState для правильного получения и записи состояния их экземпляров.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database
 */
abstract class LongObject extends RestorableAdapter implements reflection\Reflect, identification\OID, memento\Originator, Interpreter{
  use reflection\TReflect;
  use memento\TOriginator;
  use identification\TOID;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['\$([A-Z\/a-z_]+):([0-9]+)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    /**
     * @var string $m Лексемы.
     */
    $m = parent::reestablish($string);
    /**
     * @var LongObject $o Имя класса восстанавливаемого объекта.
     */
    $o = str_replace('/', '\\', $m[1]);

    return $o::getProxy((int) $m[2]);
  }

  /**
   * Метод создает объектную ссылку вида: $/имяКласса:идентификаторОбъекта. В имени класса для разделение пакетов используется символ /.
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    if(!$this->isOID()){
      throw new NotFoundDataException('Объект не идентифицирован.');
    }

    return '$/' . str_replace('\\', '/', get_class($this)) . ':' . $this->getOID();
  }
}
