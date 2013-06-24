<?php
namespace PPHP\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\io as io;

/**
 * Класс позволяет работать с ini файлами.
 * Работа с файлом доступна по средствам работы со свойствами класса.
 * При работе с файлом, разделенным на секции, по средством свойств класса, используется форма: <имяСекции>_<имяКлюча>.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem
 */
class FileINI{
  /**
   * Обрабатываемый файл
   * @var File
   */
  protected $file;

  /**
   * Буфер содержимого файла
   * @var array
   */
  protected $content;

  /**
   * Является ли ini файл секционным
   * @var boolean
   */
  protected $isSection = false;

  /**
   * Изменен ли ini файл
   * @var boolean
   */
  protected $isSet = false;

  /**
   * Метод выполняет парсинг ini файла, если это небыло произведено ранее.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   */
  protected function conditParse(){
    if(!is_array($this->content)){
      try{
        $this->parse();
      }
      catch(LockException $e){
        throw $e;
      }
      catch(NotExistsException $e){
        throw $e;
      }
    }
  }

  /**
   * Метод получает содержимое ini файла в буфер
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   */
  protected function parse(){
    try{
      $reader = $this->file->getReader();
    }
    catch(LockException $e){
      throw $e;
    }
    catch(NotExistsException $e){
      throw $e;
    }
    $this->content = parse_ini_string($reader->readAll(), $this->isSection);
    $reader->close();
  }

  /**
   * @param File $file INI файл
   * @param boolean $isSection [optional] true - если файл разделен на секции, иначе - false
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае, если значение аргумента имеет неверный тип.
   * @throws NotExistsException Выбрасывается в случае отсутствия целевого файла.
   */
  function __construct(File $file, $isSection = false){
    if(!$file->isExists()){
      throw new NotExistsException('Требуемый компонент не найден в файловой системе.');
    }
    exceptions\InvalidArgumentException::verifyType($isSection, 'b');
    $this->file = $file;
    $this->isSection = $isSection;
  }

  /**
   * Метод возвращает значение ini файла.
   * @param string $section [optional] Имя целевой секции.
   * @param string $key Ключ значения.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return string|null Значение ini файла или null, если данное значение не установленно.
   */
  public function get($key, $section = null){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    exceptions\InvalidArgumentException::verifyType($section, 'Sn');
    $this->conditParse(); // Используется сквозной выброс исключений
    if(!$this->isSection){
      if(isset($this->content[$key])){
        return $this->content[$key];
      }
    }
    else{
      if(isset($this->content[$section]) && isset($this->content[$section][$key])){
        return $this->content[$section][$key];
      }
    }

    return null;
  }

  /**
   * Метод возвращает все содержимое указанной секции.
   * @param string $section Имя целевой секции.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return array|boolean Массив значений секции или false - если файл не разделен на секции или заданной секции не существует.
   */
  public function getSection($section){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    $this->conditParse(); // Используется сквозной выброс исключений
    if(!$this->isSection || !isset($this->content[$section])){
      return false;
    }

    return $this->content[$section];
  }

  /**
   * Метод устанавливает новое значение ini файлу.
   * Изменения вступят в силу после вызова метода rewrite или уничтожения объекта деструктором.
   * @param string $section [optional] Имя целевой секции.
   * @param string $key Ключ значения.
   * @param string $value Значение.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   */
  public function set($key, $value, $section = null){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    exceptions\InvalidArgumentException::verifyType($value, 's');
    exceptions\InvalidArgumentException::verifyType($section, 'Sn');
    $this->isSet = true;
    $this->conditParse(); // Используется сквозной выброс исключений
    if($this->isSection){
      if(!isset($this->content[$section]) || !is_array($this->content[$section])){
        $this->content[$section] = [];
      }
      $this->content[$section][$key] = $value;
    }
    else{
      $this->content[$key] = $value;
    }
  }

  /**
   * Метод удаляет значение из ini файла.
   * @param string $key Ключ удаляемого значения.
   * @param string $section [optional] Имя целевой секции.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если значение было успешно удалено, false - если значение не присутствовало в ini файле.
   */
  public function remove($key, $section = null){
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    exceptions\InvalidArgumentException::verifyType($section, 'Sn');
    $this->conditParse(); // Используется сквозной выброс исключений
    if(!$this->isSection){
      if(isset($this->content[$key])){
        unset($this->content[$key]);
        $this->isSet = true;

        return true;
      }
    }
    else{
      if(isset($this->content[$section]) && isset($this->content[$section][$key])){
        unset($this->content[$section][$key]);
        $this->isSet = true;

        return true;
      }
    }

    return false;
  }

  /**
   * Метод записывает изменения в ini файл.
   * @throws NotExistsException Выбрасывается в случае, если в момент записи требуемый ini файл не был найден по прежнему адресу.
   * @throws LockException Выбрасывается в случае, если требуемый ini файл заблокирован.
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при записи в файл.
   */
  public function rewrite(){
    if($this->isSet){
      if(is_array($this->content)){
        try{
          $writer = $this->file->getWriter();
        }
        catch(NotExistsException $e){
          throw new NotExistsException('Невозможно обновить ini файл, на момент обращения требуемого файла не существовало.', null, $e);
        }
        catch(LockException $e){
          throw new LockException('Невозможно обновить ini файл, на момент обращения требуемый файл был заблокирован.', $e);
        }
        $writer->clean();
        try{
          if(!$this->isSection){
            foreach($this->content as $k => $v){
              $writer->write($k . "=" . $v . "\n");
            }
          }
          else{
            foreach($this->content as $sectionName => $sectionData){
              $writer->write('[' . $sectionName . "]\n");
              foreach($sectionData as $k => $v){
                $writer->write($k . "=" . $v . "\n");
              }
            }
          }
        }
        catch(io\IOException $e){
          throw $e;
        }
        // Перехват исключений не выполняется в связи с невозможностью их появления.
        $writer->close();
      }
    }
  }

  /**
   * Метод определяет, имеется ли в ini файле данная секция.
   * @param string $section Проверяемая секция.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - если секция определена, иначе - false
   */
  public function isSectionExists($section){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    $this->conditParse(); // Используется сквозной выброс исключений
    if(!$this->isSection){
      return false;
    }
    else{
      return isset($this->content[$section]);
    }
  }

  /**
   * Метод определяет, имеется ли в ini файле заданные данные.
   * @param string $key Проверяемые данные.
   * @param string $section Проверяемая секция.
   * @throws LockException Выбрасывается в случае, если невозможно получить доступ к потоку из-за блокировки.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - если данные определены, иначе - false
   */
  public function isDataExists($key, $section = null){
    $this->conditParse(); // Используется сквозной выброс исключений
    if(!$this->isSection){
      return isset($this->content[$key]);
    }
    else{
      return isset($this->content[$section][$key]);
    }
  }

  /**
   * Метод преобразует ссылку на ключ конфигурации в команду.
   * Разделителем секции и ключа (если файл разделен на секции) является первый символ подчеркивания (_).
   * @param string $varName Ссылка на ключ конфигурации.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return \stdClass Команда имеющая следующие свойства:
   * - section - секция конфигурации или null - если файл не разделен на секции;
   * - key - ключ конфигурации.
   */
  protected function parseVarName($varName){
    exceptions\InvalidArgumentException::verifyType($varName, 'S');
    $result = new \stdClass();
    if(!$this->isSection){
      $result->section = null;
      $result->key = $varName;
    }
    else{
      $positionDelimiter = strpos($varName, '_');
      $result->section = substr($varName, 0, $positionDelimiter);
      $result->key = substr($varName, $positionDelimiter + 1);
    }

    return $result;
  }

  function __get($name){
    $name = $this->parseVarName($name);

    return $this->get($name->key, $name->section);
  }

  function __set($name, $value){
    $name = $this->parseVarName($name);
    $this->set($name->key, $value, $name->section);
  }

  function __isset($name){
    $name = $this->parseVarName($name);

    return $this->isDataExists($name->key, $name->section);
  }

  function __unset($name){
    $name = $this->parseVarName($name);
    $this->remove($name->key, $name->section);
  }
}