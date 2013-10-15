<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Класс представляет отражения блоков документации классов и их членов.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionDoc implements Reflect{
  use TReflect;

  /**
   * @var string Описание элемента.
   */
  protected $description = '';

  /**
   * @var array Теги элемента и их значения.
   */
  protected $tags = [];

  /**
   * @param string $doc Обрабатываемый PHPDoc.
   */
  public function __construct($doc){
    $doc = explode("\n", $doc);
    unset($doc[0]);
    unset($doc[count($doc)]);
    array_map(function ($line){
      $line = substr(trim($line), 2);
      if($line[0] == '@'){
        $spacePoint = strpos($line, ' ');
        $tagName = substr($line, 1, $spacePoint - 1);
        if(array_key_exists($tagName, $this->tags) === false){
          $this->tags[$tagName] = [];
        }
        $this->tags[$tagName][] = substr($line, $spacePoint + 1);
      }
      else{
        $this->description .= $line . "\n";
      }
    }, $doc);
  }

  /**
   * @return string Описание элемента.
   */
  public function getDescription(){
    return $this->description;
  }

  /**
   * Метод возвращает массив тегов с данным именем для элемента.
   * @param string $name Имя тега.
   * @return string[] Значения тегов.
   */
  public function getTag($name){
    return $this->tags[$name];
  }

  /**
   * Метод определяет, имеется ли указанный тег документации.
   * @param string $name Имя целевого тега.
   * @return boolean true - если тег установлен, иначе - false.
   */
  public function hasTag($name){
    return array_key_exists($name, $this->tags) !== false;
  }
}