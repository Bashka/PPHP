<?php
namespace PPHP\dev\documentation\generator;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;
use PPHP\tools\classes\standard\fileSystem\File;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\interpreter\Metamorphosis;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;
use PPHP\tools\patterns\metadata\reflection\ReflectionDoc;

class Element implements Interpreter, Metamorphosis{
  /**
   * @var File
   */
  protected $file;

  protected $name;

  protected $namespace;

  /**
   * @var ReflectionClass
   */
  protected $reflectionClass;

  protected function interpreterProperties(){
    $properties = $this->reflectionClass->getProperties();
    if(count($properties) > 0){
      $node = '<node name="properties" prog_lang="custom-colors" readonly="False" tags="" unique_id="' . Generator::getUniqueId() . '">';

      $private = '';
      $protected = '';
      $public = '';

      foreach($properties as $property){
        $nodeProperty = ''; // Текущий узел
        $doc = new ReflectionDoc($property->getDocComment());

        // Маркер видимости
        if($property->isPrivate()){
          $visibility = '-';
        }
        elseif($property->isProtected()){
          $visibility = '#';
        }
        else{
          $visibility = '+';
        }
        $nodeProperty .= '<rich_text>' . $visibility . '</rich_text>';
        // Имя
        $nodeProperty .= '<rich_text foreground="#00000000ffff" weight="heavy" ' . (($property->isStatic())? 'underline="single"' : '') . '>' . $property->getName() . ' </rich_text>';
        // Тег var
        if($doc->hasTag('var')){
          $tag = explode(' ', $doc->getTag('var')[0]);
          $type = $tag[0];
          unset($tag[0]);
          $desctiption = htmlspecialchars(implode(' ', $tag));
          $nodeProperty .= '<rich_text foreground="#aaaaaaaaaaaa" scale="sup">' . $type . "\n" . '</rich_text>';
          $nodeProperty .= '<rich_text underline="single">Описание:'."\n".'</rich_text>';
          $nodeProperty .= '<rich_text>' . $desctiption . "\n\n" . '</rich_text>';
        }
        // Выбор контейнера
        if($property->isPrivate()){
          if($private == ''){
            $private = '<rich_text scale="h1">Private'."\n".'</rich_text>';
          }
          $private .= $nodeProperty;
        }
        elseif($property->isProtected()){
          if($protected == ''){
            $protected = '<rich_text scale="h1">Protected'."\n".'</rich_text>';
          }
          $protected .= $nodeProperty;
        }
        else{
          if($public == ''){
            $public = '<rich_text scale="h1">Public'."\n".'</rich_text>';
          }
          $public .= $nodeProperty;
        }
      }
      $node .= $private;
      $node .= $protected;
      $node .= $public;
      $node .= '</node>';

      return $node;
    }
    else{
      return '';
    }
  }

  protected function interpreterMethods(){
    $methods = $this->reflectionClass->getMethods();
    if(count($methods) > 0){
      $node = '<node name="methods" prog_lang="custom-colors" readonly="False" tags="" unique_id="' . Generator::getUniqueId() . '">';

      $private = '';
      $protected = '';
      $public = '';

      foreach($methods as $method){
        $nodeMethod = ''; // Текущий узел.
        // Поиск документации к методу.
        $doc = new ReflectionDoc($method->getDocComment());
        if($doc->hasTag('prototype')){
          $detail = $doc->getDescription();
          $doc = new ReflectionDoc((new \ReflectionMethod($doc->getTag('prototype')[0], $method->getName()))->getDocComment());
        }
        else{
          $detail = '';
        }

        // Маркер видимости
        if($method->isPrivate()){
          $visibility = '-';
        }
        elseif($method->isProtected()){
          $visibility = '#';
        }
        else{
          $visibility = '+';
        }
        $nodeMethod .= '<rich_text>' . $visibility . '</rich_text>';
        // Имя
        $parameters = $method->getParameters();
        $args = '(';
        $parseParams = [];
        foreach($parameters as $param){
          $parseParam = '';
          // Передача по ссылке
          if($param->isPassedByReference()){
            $parseParam .= htmlspecialchars('&');
          }
          // Имя аргумента
          $parseParam .= $param->getName();
          // Значение по умолчанию
          if($param->isDefaultValueAvailable()){
            $value = $param->getDefaultValue();
            if(is_null($value)){
              $value = 'null';
            }
            elseif(is_string($value)){
              $value = '\'' . $value . '\'';
            }
            $parseParam .= ' = ' . $value;
          }
          $parseParams[] = $parseParam;
        }
        $args .= implode(', ', $parseParams) . ')';
        $nodeMethod .= '<rich_text foreground="#00000000ffff" weight="heavy" ' . (($method->isStatic())? 'underline="single"' : '') . ' ' . (($method->isAbstract())? 'style="italic"' : '') . '>' . $method->getName() . '</rich_text>';
        $nodeMethod .= '<rich_text>' . $args . '</rich_text>';

        if($doc->hasTag('return')){
          $return = explode(' ', $doc->getTag('return')[0]);
          $nodeMethod .= '<rich_text foreground="#aaaaaaaaaaaa" scale="sup"> ' . $return[0] . "\n" . '</rich_text>';
        }
        else{
          $nodeMethod .= '<rich_text>'."\n".'</rich_text >';
        }

        // Описание
        $nodeMethod .= '<rich_text underline="single">Описание:'."\n".'</rich_text>';
        $description = trim($doc->getDescription().$detail);
        $nodeMethod .= '<rich_text>'.htmlspecialchars($description)."\n".'</rich_text>';

        // Аргументы
        if($doc->hasTag('param')){
          $nodeMethod .= '<rich_text underline="single">Аргументы:'."\n".'</rich_text>';
          $params = $doc->getTag('param');
          foreach($params as $param){
            $param = explode(' ', $param);
            // Тип
            $type = $param[0];
            unset($param[0]);

            // Имя
            if(isset($param[1])){
              $name = substr($param[1], 1);
              unset($param[1]);
            }
            else{
              $name = '';
            }

            // Обязательность
            if(isset($param[2]) && $param[2] == '[optional]'){
              $optional = true;
              unset($param[2]);
            }
            else{
              $optional = false;
            }
            $nodeMethod .= '<rich_text '.(($optional)? 'style="italic"' : '').'>• '.$name.' </rich_text><rich_text foreground="#aaaaaaaaaaaa" scale="sup">'.$type.'</rich_text><rich_text> - '.htmlspecialchars(implode(' ', $param))."\n".'</rich_text>';
          }
        }

        // Возврат
        if($doc->hasTag('return')){
          $return = explode(' ', $doc->getTag('return')[0]);
          $nodeMethod .= '<rich_text underline="single">Возвращаемое значение:'."\n".'</rich_text>';
          $nodeMethod .= '<rich_text foreground="#aaaaaaaaaaaa">'.$return[0].'</rich_text>';
          unset($return[0]);
          $nodeMethod .= '<rich_text> - '.implode(' ', $return)."\n".'</rich_text>';
        }

        // Выбрасываемые исключения
        if($doc->hasTag('throws')){
          $nodeMethod .= '<rich_text underline="single">Исключения:'."\n".'</rich_text>';
          $throws = $doc->getTag('throws');
          foreach($throws as $exc){
            $exc = explode(' ', $exc);
            // Тип
            $type = $exc[0];
            unset($exc[0]);

            $nodeMethod .= '<rich_text>• '.$type.' </rich_text><rich_text> - '.implode(' ', $exc)."\n".'</rich_text>';
          }
        }

        $nodeMethod .= '<rich_text> '."\n".'</rich_text>';

        // Выбор контейнера
        if($method->isPrivate()){
          if($private == ''){
            $private = '<rich_text scale="h1">Private'."\n".'</rich_text>';
          }
          $private .= $nodeMethod;
        }
        elseif($method->isProtected()){
          if($protected == ''){
            $protected = '<rich_text scale="h1">Protected'."\n".'</rich_text>';
          }
          $protected .= $nodeMethod;
        }
        else{
          if($public == ''){
            $public = '<rich_text scale="h1">Public'."\n".'</rich_text>';
          }
          $public .= $nodeMethod;
        }
      }
      $node .= $private;
      $node .= $protected;
      $node .= $public;
      $node .= '</node>';

      return $node;
    }
    else{
      return '';
    }
  }

  /**
   * Метод восстанавливает объект из другого объекта.
   * @param File $object Исходный объект.
   * @param string $driver Полное имя родительского пакета.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!($object instanceof File)){
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается [File] вместо [' . gettype($object) . ']');
    }
    if(!$object->isExists()){
      throw new exceptions\NotFoundDataException('Отсутствует запрашиваемый компонент [' . $object->getAddress() . '].');
    }

    return new Element($object, $object->getName(), $driver);
  }

  public function __construct($file, $name, $namespace){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    $this->file = $file;
    $this->name = substr($name, 0, strlen($name) - 4);
    $this->namespace = $namespace;
    $this->reflectionClass = new ReflectionClass($this->namespace . '\\' . $this->name);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    $node = '<node name="' . $this->name . '" prog_lang="custom-colors" readonly="False" tags="" unique_id="' . Generator::getUniqueId() . '">';
    $fullName = $this->namespace . '\\' . $this->name;
    // Имя
    $node .= '<rich_text underline="single">Имя:</rich_text><rich_text weight="heavy" '.(($this->reflectionClass->isAbstract())? 'style="italic"' : '').'> ' . $this->name . "\n" . '</rich_text>';
    // Полное имя
    $node .= '<rich_text underline="single">Полное имя:</rich_text><rich_text> ' . $fullName . "\n" . '</rich_text>';
    // Родительский класс
    $parentClass = $this->reflectionClass->getParentClass();
    if($parentClass !== false){
      $node .= '<rich_text underline="single">Родительский класс:</rich_text><rich_text> ' . $parentClass->getName(). "\n" . '</rich_text>';
    }
    // Реализуемые интерфейсы
    $interfaces = $this->reflectionClass->getInterfaceNames();
    if(count($interfaces) > 0){
      $node .= '<rich_text underline="single">Реализуемые интерфейсы:</rich_text><rich_text> ' . implode(', ', $interfaces). "\n" . '</rich_text>';
    }
    // Используемые traits
    $traits = $this->reflectionClass->getTraitNames();
    if(count($traits) > 0){
      $node .= '<rich_text underline="single">Используемые traits:</rich_text><rich_text> ' . implode(', ', $traits). "\n" . '</rich_text>';
    }
    // Описание
    $node .= '<rich_text underline="single">Описание:'."\n".'</rich_text><rich_text>' . htmlspecialchars($this->reflectionClass->getDoc()->getDescription()) . '</rich_text>';
    // Свойства
    $node .= $this->interpreterProperties();
    // Методы
    $node .= $this->interpreterMethods();
    $node .= '</node>';

    return $node;
  }
}