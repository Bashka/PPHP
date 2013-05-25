<?php
namespace PPHP\tools\classes\standard\fileSystem;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс является представлением компонента файловой системы и определяет основные механизмы манипулирования им.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem
 */
abstract class ComponentFileSystem{
  /**
   * Имя компонента
   * @var string
   */
  protected $name;

  /**
   * Каталог, в котором располагается данный компонент.
   * @var Directory
   */
  protected $location;

  /**
   * Метод создает и возвращает компонент ФС по его полному адресу от корня системы.
   * @static
   *
   * @param string $address        Полный адрес компонента.
   * @param string $componentClass Имя класса компонента.
   *
   * @return mixed
   */
  private static final function constructFromAddress($address, $componentClass){
    $address = substr($address, strlen($_SERVER['DOCUMENT_ROOT']) + 1);
    $components = explode('/', $address);
    $resultComponent = array_pop($components);
    $rootDir = new RootDirectory();
    // Если создаваемый компонент находится в корневом каталоге
    if(count($components) == 0){
      return new $componentClass($resultComponent, $rootDir);
    }
    // Иначе создаем промежуточные каталоги от корневого каталога до создаваемого компонента.
    else{
      $dir = new Directory(array_shift($components), $rootDir);
      foreach($components as $component){
        $dir = new Directory($component, $dir);
      }
      return new $componentClass($resultComponent, $dir);
    }
  }

  /**
   * Метод копирует компонента в данный каталог.
   * @abstract
   *
   * @param Directory $location Целевой каталог.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если целевой каталог уже содержит копируемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  abstract public function copyPaste(Directory $location);

  /**
   * Метод удаляет текущий компонент из файловой системы.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   *
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  abstract public function delete();

  /**
   * Метод возвращает размер в байтах данного компонента.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return integer Размер компонента в байтах.
   */
  abstract public function getSize();

  /**
   * Метод определяет, существует ли вызывающий компонент на момент вызова метода.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если родительского каталога не существует.
   * @return boolean true - если компонент на момент вызова метода существует в файловой системе, иначе - false.
   */
  abstract public function isExists();

  /**
   * Метод создает и возвращает объект класса File по его полному адресу от корня системы.
   *
   * @static
   *
   * @param string $address Полный адрес компонента.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа..
   * @return File
   */
  public static final function constructFileFromAddress($address){
    exceptions\InvalidArgumentException::verifyType($address, 'S');

    return self::constructFromAddress(str_replace('\\', '/', $address), '\PPHP\tools\classes\standard\fileSystem\File');
  }

  /**
   * Метод создает и возвращает объект класса Directory по его полному адресу от корня системы.
   *
   * @static
   *
   * @param string $address Полный адрес компонента.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return Directory
   */
  public static final function constructDirFromAddress($address){
    exceptions\InvalidArgumentException::verifyType($address, 'S');

    return self::constructFromAddress(str_replace('\\', '/', $address), '\PPHP\tools\classes\standard\fileSystem\Directory');
  }

  /**
   * Метод изменяет имя компонента на заданное, если это возможно.
   *
   * @param string $newName Новое имя компонента.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если переименование компонента приведет к дублированию.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function rename($newName){
    exceptions\InvalidArgumentException::verifyType($newName, 'S');
    if(strpos($newName, '/') > -1){
      throw exceptions\InvalidArgumentException::getValidException('[^/]', $newName);
    }
    if(!$this->isExists()){
      throw new NotExistsException('Используемый компонент [' . $this->getAddress() . '] не найден в файловой системе.');
    }
    // Проверка на дублирование выполняется в конкретных классах

    $newAddress = $this->getLocationAddress() . '/' . $newName;
    $result = rename($this->getAddress(), $newAddress);
    if($result){
      $this->name = $newName;
    }
    return $result;
  }

  /**
   * Метод перемещает компонент в данный каталог.
   *
   * @param Directory $location Целевой каталог.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если целевой каталог уже содержит компонент с тем же именем, что и перемещаемый.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @throws exceptions\RuntimeException Выбрасывается в случае нарушения логики работы файловой системы путем перемещения компонента в себя.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function move(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Используемый компонент ' . $this->getAddress() . ' не найден в файловой системе.');
    }
    // Проверка на дублирование выполняется в конкретных классах
    // Проверка на рекурсию выполняется в конкретных классах

    $result = rename($this->getAddress(), $location->getAddress() . '/' . $this->getName());
    if($result){
      $this->location = $location;
    }
    return $result;
  }

  /**
   * Метод возвращает ссылку на каталог, содержащий данный компонент.
   * @return Directory Родительский каталог.
   */
  public function getLocation(){
    return $this->location;
  }

  /**
   * Метод возвращает имя компонента.
   * @return string Имя вызывающего компонента.
   */
  public function getName(){
    return $this->name;
  }

  /**
   * Метод возвращает полный адрес вызывающего компонента.
   * @return string Полный адрес вызывающего компонента.
   */
  public function getAddress(){
    return $this->getLocationAddress() . '/' . $this->getName();
  }

  /**
   * Метод возвращает адрес родительского каталога вызывающего компонента.
   * @return string Адрес родительского каталога.
   */
  public function getLocationAddress(){
    return $this->location->getAddress();
  }

  /**
   * @param string    $name     Имя компонента.
   * @param Directory $location Расположение компонента.
   */
  public function __construct($name, Directory $location){
    $this->name = $name;
    $this->location = $location;
  }
}
