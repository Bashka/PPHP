<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Класс является представлением компонента файловой системы и определяет основные механизмы манипулирования им.
 */
abstract class ComponentFileSystem{
  /**
   * Имя компонента
   * @var string
   */
  protected $name;

  /**
   * Каталог, в котором располагается данный компонент.
   * @var \PPHP\tools\classes\standard\fileSystem\Directory
   */
  protected $location;

  /**
   * Метод создает и возвращает компонент ФС по его полному адресу от корня системы.
   * @static
   * @param string $address Полный адрес компонента.
   * @param $componentClass Класс компонента
   * @return mixed
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  static private final function constructFromAddress($address, $componentClass){
    if(!is_string($address)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $address);
    }
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
   * Метод создает и возвращает объект класса \PPHP\tools\classes\standard\fileSystem\File по его полному адресу от корня системы.
   * @static
   * @param string $address Полный адрес компонента.
   * @return \PPHP\tools\classes\standard\fileSystem\File
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передан неверный тип.
   */
  static public final function constructFileFromAddress($address){
    return self::constructFromAddress(str_replace('\\', '/', $address), '\PPHP\tools\classes\standard\fileSystem\File');
  }

  /**
   * Метод создает и возвращает объект класса \PPHP\tools\classes\standard\fileSystem\Directory по его полному адресу от корня системы.
   * @static
   * @param string $address Полный адрес компонента.
   * @return \PPHP\tools\classes\standard\fileSystem\Directory
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передан неверный тип.
   */
  static public final function constructDirFromAddress($address){
    return self::constructFromAddress(str_replace('\\', '/', $address), '\PPHP\tools\classes\standard\fileSystem\Directory');
  }

  /**
   * Метод изменяет имя компонента на заданное, если это возможно.
   * @abstract
   * @param string $newName Новое имя компонента.
   * @throws ComponentDuplicationException Выбрасывается в случае, если переименование компонента приведет к дублированию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве нового имени передано значение не string типа, или если новое имя содержит симол /.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function rename($newName){
    $newAddress = $this->getLocationAddress() . '/' . $newName;
    $result = rename($this->getAddress(), $newAddress);
    if($result)
      $this->name = $newName;
    return $result;
  }

  /**
   * Метод перемещает компонент в данный каталог.
   * @abstract
   * @param Directory $location Целевой каталог.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит компонент с тем же именем, что и перемещаемый.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function move(Directory $location){
    $result = rename($this->getAddress(), $location->getAddress() . '/' . $this->getName());
    if($result)
      $this->location = $location;
    return $result;
  }

  /**
   * Метод копирует компонента в данный каталог.
   * @abstract
   * @param Directory $location Целевой каталог.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит копируемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  abstract public function copyPaste(Directory $location);

  /**
   * Метод удаляет текущий компонент из файловой системы.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   * @return boolean true - если компонент был успешно удален.
   */
  abstract public function delete();

  /**
   * Метод возвращает размер в байтах данного компонента.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return integer
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
   * @param string $name Имя компонента.
   * @param \PPHP\tools\classes\standard\fileSystem\Directory $location Расположение компонента.
   */
  public function __construct($name, Directory $location){
    $this->name = $name;
    $this->location = $location;
  }
}
