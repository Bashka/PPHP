<?php
namespace PPHP\dev\documentation\generator;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\interpreter\Restorable;

class DocFile implements Interpreter, Restorable{
  protected $lines = [];

  /**
   * Метод позволяет определить допустимость интерпретации исходной строки в объект.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return boolean true - если интерпретация возможна, иначе - false.
   */
  public static function isReestablish($string, $driver = null){
    return true;
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    $isCode = false;
    $string = explode("\n", $string);
    $chars = 0;
    foreach($string as &$line){
      if(!empty($line)){
        $startTag = '';
        $content = new String('');
        $endTag = '';

        $line = new String($line);
        /**
         * @var \PPHP\tools\classes\standard\baseType\String $tag
         */
        $tag = $line->nextComponent(' ');
        if($tag !== false){
          switch($tag->getVal()){
            case 'h1:':
              $startTag = '<rich_text scale="h1">';
              $content = new String("\n".$line->sub()->getVal()."\n");
              $endTag = '</rich_text>';
              break;
            case 'h2:':
              $startTag = '<rich_text scale="h2">';
              $content = new String("\n".$line->sub()->getVal()."\n");
              $endTag = '</rich_text>';
              break;
            case 'h3:':
              $startTag = '<rich_text scale="h3">';
              $content = new String($line->sub()->getVal()."\n");
              $endTag = '</rich_text>';
              break;
            case '-':
              $startTag = '<rich_text>';
              $content = new String('• '.$line->sub()->getVal()."\n");
              $endTag = '</rich_text>';
              break;
            case 'img:':
              $startTag = '<encoded_png char_offset="'.$chars.'">';
              $imgfile = $_SERVER['DOCUMENT_ROOT'].'/PPHP/dev/documentation/imgs'.str_replace('\\', '/', substr($driver, 5)).'/'.$line->sub()->getVal();
              $content = new String(base64_encode(fread(fopen($imgfile, "r"), filesize($imgfile))));
              $endTag = '</encoded_png><rich_text>'."\n".'</rich_text>';
              break;
            case 'code:':
              $codeOption = $line->sub()->getVal();
              if($codeOption != 'end'){
                $codeOption = new String($codeOption);
                $lang = $codeOption->nextComponent(' ')->getVal();
                $codeHeight = $codeOption->sub()->getVal();
                $isCode = true;
                $startTag = '<codebox char_offset="'.$chars.'" frame_height="'.$codeHeight.'" frame_width="100" highlight_brackets="True" show_line_numbers="True" syntax_highlighting="'.$lang.'" width_in_pixels="False">';
              }
              else{
                $isCode = false;
                $endTag = '</codebox><rich_text>'."\n".'</rich_text>';
              }
              break;
            default:
              if(!$isCode){
                $startTag = '<rich_text>';
                $content = new String('    '.$line->getVal()."\n");
                $endTag = '</rich_text>';
              }
              else{
                $content = new String($line->getVal()."\n");
              }
          }
        }
        else{
          if(!$isCode){
            $startTag = '<rich_text>';
            $content = new String('    '.$line->getVal()."\n");
            $endTag = '</rich_text>';
          }
          else{
            $content = new String($line->getVal()."\n");
          }
        }

        $chars += $content->count();
        $line = $startTag.$content->getVal().$endTag;
      }
    }
    return new DocFile($string);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    return implode('', $this->lines);
  }

  public function __construct($lines){
    $this->lines = $lines;
  }
}