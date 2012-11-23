<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Класс позволяет работать с ini файлами
 */
class FileINI{
  /**
   * Обрабатываемый файл
   * @var \PPHP\tools\classes\standard\fileSystem\File
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
   * Метод получает содержимое ini файла в буфер
   */
  protected function parse(){
    $reader = $this->file->getReader();
    $this->content = parse_ini_string($reader->readAll(), $this->isSection);
    $reader->close();
  }

  /**
   * @param File $file INI файл
   * @param boolean $isSection true - если файл разделен на секции, иначе - false
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если значение аргумента имеет неверный тип
   */
  function __construct(File $file, $isSection = false){
    if(!is_bool($isSection)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('boolean', $isSection);
    }
    $this->file = $file;
    $this->isSection = $isSection;
  }

  /**
   * Метод возвращает значение ini файла
   * @param null $section Секция
   * @param string $key Ключ значения
   * @return string|null Значение ini файла или null, если данное значение не установленно
   */
  public function get($key, $section = null){
    if(!is_array($this->content)){
      $this->parse();
    }
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
   * @return array|boolean Массив значений секции или false - если файл не разделен на секции или заданной секции не существует.
   */
  public function getSection($section){
    if(!$this->isSection || !isset($this->content[$section])){
      return false;
    }
    return $this->content[$section];
  }

  /**
   * Метод устанавливает новое значение ini файлу
   * Изменения вступят в силу после уничтожения объекта деструктором
   * @param null $section Секция
   * @param string $key Ключ значения
   * @param string $value Значение
   */
  public function set($key, $value, $section = null){
    $this->isSet = true;
    if(!is_array($this->content)){
      $this->parse();
    }
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
   * @param null $section Секция.
   * @param string $key Ключ удаляемого значения.
   * @return boolean true - если значение было успешно удалено, false - если значение не присутствовало в ini файле.
   */
  public function remove($key, $section = null){
    if(!is_array($this->content)){
      $this->parse();
    }
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
   * Метод записывает изменения в ini файл
   * @throws NotExistsException Выбрасывается в случае, если в момент записи требуемый ini файл не был найден по прежнему адресу
   * @throws LockException Выбрасывается в случае, если требуемый ini файл заблокирован
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
        $writer->close();
      }
    }
  }

  /**
   * Метод определяет, имеется ли в ini файле данная секция
   * @param string $section Проверяемая секция
   * @return boolean true - если секция определена, иначе - false
   */
  public function isSectionExists($section){
    if(!is_array($this->content)){
      $this->parse();
    }
    if(!$this->isSection){
      return false;
    }
    else{
      return isset($this->content[$section]);
    }
  }

  /**
   * Метод определяет, имеется ли в ini файле заданные данные
   * @param string $key Проверяемые данные
   * @param null $section Секция
   * @return boolean true - если данные определены, иначе - false
   */
  public function isDataExists($key, $section = null){
    if(!is_array($this->content)){
      $this->parse();
    }
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
   * @return \stdClass Команда имеющая следующие свойства:
   * - section - секция конфигурации или null - если файл не разделен на секции;
   * - key - ключ конфигурации.
   */
  protected function parseVarName($varName){
    $result = new \stdClass();
    if(!$this->isSection){
      $result->section = null;
      $result->key = $varName;
    }
    else{
      $positionDelimiter = strpos($varName, '_');
      $result->section = substr($varName, 0, $positionDelimiter);
      $result->key = substr($varName, $positionDelimiter+1);
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