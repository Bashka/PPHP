<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для предоставления дополнительной логики строкам.
 */
class String{
  /**
   * Оборачиваемое значение
   * @var string
   */
  protected $val;

  function __construct($val){
    $this->val = $val;
  }

  /**
   * Метод возвращает текущее значение строки.
   * @return string
   */
  public function getVal(){
    return $this->val;
  }

  /**
   * Множество символов, используемых для формирования строк.
   * @var string[]
   */
  protected static $symbol = [
    [
      'a','b','c','d','e','f',
      'g','h','i','j','k','l',
      'm','n','o','p','r','s',
      't','u','v','x','y','z',
      'A','B','C','D','E','F',
      'G','H','I','J','K','L',
      'M','N','O','P','R','S',
      'T','U','V','X','Y','Z'
    ],
    [
      'а','б','в','г','д','е',
      'ё','ж','з','и','й','к',
      'л','м','н','о','п','р',
      'с','т','у','ф','х','ц',
      'ч','ш','щ','ъ','ы','ь',
      'э','ю','я','А','Б','В',
      'Г','Д','Е','Ё','Ж','З',
      'И','Й','К','Л','М','Н',
      'О','П','Р','С','Т','У',
      'Ф','Х','Ц','Ч','Ш','Щ',
      'Ъ','Ы','Ь','Э','Ю','Я'
    ],
    [
      '1','2','3','4','5','6',
      '7','8','9','0'
    ],
    [
      '.',',','(',')','[',']',
      '!','?','&','^','%','@',
      '*','$','<','>','|','+',
      '-','{','}','~'
    ]
  ];

  /**
   * Метод формирует случайную строку зананной длины.
   * @static
   * @param integer $length Длина строки.
   * @param integer $srand Число для инициализации генератора случайных чисел.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный форма.
   * @return \PPHP\tools\classes\standard\baseType\String
   */
  public static function generate($length, $srand = null){
    $string = '';
    if(!is_int($length))
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $length);
    $srand = !is_null($srand)? $srand : time();
    if(!is_int($srand))
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $srand);

    srand($srand);
    while($length > 0){
      $index = $length % 4;
      $string .= self::$symbol[$index][rand(0, count(self::$symbol[$index]) - 1)];
      $length--;
    }
    return new static($string);
  }
}
