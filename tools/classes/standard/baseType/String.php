<?php
namespace PPHP\tools\classes\standard\baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для предоставления дополнительной логики строкам.
 * Допустимый тип: тип integer; тип float; тип boolean; тип string.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class String extends Wrapper implements \ArrayAccess, \Iterator{
  /**
   * Дополнение строки до указанной длины слева.
   */
  const PAD_LEFT = STR_PAD_LEFT;

  /**
   * Дополнение строки до указанной длины справа.
   */
  const PAD_RIGHT = STR_PAD_RIGHT;

  /**
   * Дополнение строки до указанной длины с обоих сторон поровну.
   */
  const PAD_BOTH = STR_PAD_BOTH;

  /**
   * Множество символов, используемых для формирования строк.
   * @var string[]
   */
  protected static $symbol = [['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z'], ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'], ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'], ['.', ',', '(', ')', '[', ']', '!', '?', '&', '^', '%', '@', '*', '$', '<', '>', '|', '+', '-', '{', '}', '~']];

  /**
   * Внутренний указатель компонента.
   * Указатель может быть задан программно или изменяться автоматически при вызове некоторых методов.
   * Указатель определяет текущий байт строки.
   * @var integer
   */
  protected $point = 0;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [
      '.*'
    ];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    exceptions\InvalidArgumentException::verifyType($string, 's');
    return new self((string) $string);
  }

  function __construct($val){
    if(!is_string($val)){
      throw exceptions\InvalidArgumentException::getTypeException('string', gettype($val));
    }
    parent::__construct($val);
  }

  /**
   * Метод формирует случайную строку зананной длины.
   * @static
   *
   * @param integer $length Длина строки.
   * @param integer $srand Число для инициализации генератора случайных чисел.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return String
   */
  public static function generate($length, $srand = null){
    exceptions\InvalidArgumentException::verifyType($length, 'i');
    $srand = !is_null($srand)? $srand : time();
    exceptions\InvalidArgumentException::verifyType($srand, 'if');

    $string = '';
    srand($srand);
    while($length > 0){
      $index = $length % 4;
      $string .= self::$symbol[$index][rand(0, count(self::$symbol[$index]) - 1)];
      $length--;
    }
    return new self($string);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset){
    return ($offset > -1 && $offset < $this->count());
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset){
    return $this->sub($offset, 1)->getVal();
  }

  /**
   * Присваивание значение элементу запщено.
   *
   * @param $offset
   * @param $value
   *
   * @throws exceptions\RuntimeException Выбрасывается в случае вызова метода.
   */
  public function offsetSet($offset, $value){
    throw new exceptions\RuntimeException('Невозможно модифицировать объект.');
  }

  /**
   * Удаление значение из элемента запрещено.
   * @param $offset
   * @throws exceptions\RuntimeException Выбрасывается в случае вызова метода.
   */
  public function offsetUnset($offset){
    throw new exceptions\RuntimeException('Невозможно модифицировать объект.');
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current(){
    return iconv_substr($this->val, $this->point, 1, 'UTF-8');
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next(){
    $this->point++;
  }

  /**
   * Сдвигает внутренний указатель на одну позицию назад.
   * @return bool
   */
  public function prev(){
    if($this->point > 0){
      $this->point--;
      return true;
    }
    else{
      return false;
    }
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return scalar scalar on success, or null on failure.
   */
  public function key(){
    return $this->point;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   */
  public function valid(){
    if($this->point < 0 || $this->point >= $this->count()){
      return false;
    }
    return true;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind(){
    $this->point = 0;
  }

  /**
   * Метод возвращает длину строки в байтах.
   * @return integer Число байт, занимаемых строкой.
   */
  public function length(){
    return strlen($this->val);
  }

  /**
   * Метод возвращает число символов в строке для UTF-8 кодировки.
   * @return integer Число символов в строке.
   */
  public function count(){
    return iconv_strlen($this->val, 'UTF-8');
  }

  /**
   * Метод возвращает подстроку строки.
   *
   * @param integer $start [optional] Позиция начального символа. Если параметр не задан, используется позиция, на которую ссылается внутренний указатель.
   * @param integer $length [optional] Число отбираемых символов справа от начального символа если значение положительное, и слева если отрицательное. Если параметр не задан, выбирается все символы до конца строки.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующая подстрока.
   */
  public function sub($start = null, $length = null){
    $start = ($start === null)? $this->point : $start;
    $length = ($length === null)? $this->count() : $length;

    exceptions\InvalidArgumentException::verifyType($start, 'i');
    exceptions\InvalidArgumentException::verifyType($length, 'i');

    if($length < 0){
      $start = $start+$length;
      if($start < 0){
        $length = $length+(-$start);
        $start = 0;
      }
      $length = -$length;
    }
    return new static(iconv_substr($this->val, $start, $length, 'UTF-8'));
  }

  /**
   * Метод возвращает подстроку строки в байтах.
   *
   * @param integer $start [optional] Позиция начального байта.
   * @param integer $length [optional] Число отбираемых символов справа от начального символа если значение положительное, и слева если отрицательное. Если параметр не задан, выбирается все символы до конца строки.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующая подстрока.
   */
  public function subByte($start = null, $length = null){
    $start = ($start === null)? $this->point : $start;
    $length = ($length === null)? $this->count() : $length;

    exceptions\InvalidArgumentException::verifyType($start, 'i');
    exceptions\InvalidArgumentException::verifyType($length, 'i');

    if($length < 0){
      $start = $start+$length;
      if($start < 0){
        $length = $length+(-$start);
        $start = 0;
      }
      $length = -$length;
    }
    return new static(substr($this->val, $start, $length));
  }

  /**
   * Метод возвращает подстроку из указанно числа символов начиная с первого.
   *
   * @param integer $length Отбираемое число символов.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующая подстрока.
   */
  public function subLeft($length){
    exceptions\InvalidArgumentException::verifyType($length, 'i');
    exceptions\InvalidArgumentException::verifyVal($length, 'i > 0');

    return $this->sub(0, $length);
  }

  /**
   * Метод возвращает подстроку из указанного числа символов начиная с последнего.
   *
   * @param integer $length Отбираемое число символов.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующая подстрока.
   */
  public function subRight($length){
    exceptions\InvalidArgumentException::verifyType($length, 'i');
    exceptions\InvalidArgumentException::verifyVal($length, 'i > 0');

    return $this->sub($this->count() - $length - 1);
  }

  /**
   * Метод дополняет строку до указанной длины символов.
   *
   * @param integer $length Требуемая длина строки. Если текущая длина больше или равна требуемой, то дополнения не происходит.
   * @param string $char [optional] Символ, заполняющий недостающую длину строки. По умолчанию пробел.
   * @param integer $type Тип дополнения. Одна из констрант данного класса тип PAD_*.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующая строка.
   */
  public function pad($length, $char = ' ', $type = self::PAD_LEFT){
    exceptions\InvalidArgumentException::verifyType($length, 'i');
    exceptions\InvalidArgumentException::verifyVal($length, 'i > 0');
    exceptions\InvalidArgumentException::verifyType($char, 'S');
    exceptions\InvalidArgumentException::verifyType($type, 'i');

    $count = $this->count();
    if($count < $length){
      $val = $this->getVal();
      $difference = $length - $count;
      for($i = 0; $i < $difference; $i++){
        if($type == self::PAD_LEFT){
          $val = $char . $val;
        }
        elseif($type == self::PAD_RIGHT){
          $val = $val . $char;
        }
        elseif($type == self::PAD_BOTH){
          if($i % 2 == 0){
            $val = $char . $val;
          }
          else{
            $val = $val . $char;
          }
        }
      }
      return new static($val);
    }
    else{
      return $this;
    }
  }

  /**
   * Метод заменяет указанные подстроки в строке на данные.
   *
   * @param string $search Искомая подстрока.
   * @param string $replace Заменяющая подстрока.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Результирующая строка.
   */
  public function replace($search, $replace){
    exceptions\InvalidArgumentException::verifyType($search, 'S');
    exceptions\InvalidArgumentException::verifyType($replace, 's');

    return new static(str_replace($search, $replace, $this->val));
  }

  /**
   * Метод заменяет указанный шаблон на заданную строку.
   *
   * @param string $pattern Регулярное выражение для поиска.
   * @param string $replacement Замещающая строка.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Результирующая строка.
   */
  public function change($pattern, $replacement){
    exceptions\InvalidArgumentException::verifyType($pattern, 'S');
    exceptions\InvalidArgumentException::verifyType($replacement, 's');

    return new static(preg_replace($pattern, $replacement, $this->val));
  }

  /**
   * Метод выполняет поиск указанной подстроки в строке и возвращает ее позицию.
   *
   * @param string $search Искомая строка.
   * @param boolean $registry [optional] Если true - то поиск выполняется без учета регистра, иначе - false.
   * @param boolean $beginning [optional] Если true - то поиск выполняется с начала строки, false - с конца строки.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return integer Позиция искомой подстроки в строке или -1 - если подстрока не найдена.
   */
  public function search($search, $registry = true, $beginning = true){
    exceptions\InvalidArgumentException::verifyType($search, 'S');
    exceptions\InvalidArgumentException::verifyType($registry, 'b');
    exceptions\InvalidArgumentException::verifyType($beginning, 'b');

    if($beginning){
      if($registry){
        return strpos($this->val, $search);
      }
      else{
        return stripos($this->val, $search);
      }
    }
    else{
      if($registry){
        return strrpos($this->val, $search);
      }
      else{
        return strripos($this->val, $search);
      }
    }
  }

  /**
   * Метод выполняет поиск подстроки с помощью регулярного выражения.
   *
   * @param string $pattern Регулярное выражение.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return integer Число найденых совпадений с шаблоном. 1 - если подстрока найдена и 0 - если нет.
   */
  public function match($pattern){
    exceptions\InvalidArgumentException::verifyType($pattern, 'S');

    return preg_match($pattern, $this->val);
  }

  /**
   * Метод разбивает строку на массив по указанному разделителю.
   *
   * @param string $delimiter Разделитель.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return string[] Массив подстрок.
   */
  public function explode($delimiter){
    exceptions\InvalidArgumentException::verifyType($delimiter, 'S');

    return explode($delimiter, $this->val);
  }

  /**
   * Метод возвращает MD5 хэш данной строки.
   * @return string MD5 хэш данной строки.
   */
  public function md5(){
    return md5($this->val);
  }

  /**
   * Метод возвращает SHA1 хэш данной строки.
   * @return string SHA1 хэш данной строки.
   */
  public function sha1(){
    return sha1($this->val);
  }

  /**
   * Метод выполняет верификацию строки в соответствии с маской.
   *
   * @param string $mask Маска верификации
   * Аргумент имеет структуру: <типВалидации> <ключи валидации>.
   * Возможные значения аргумента:
   * - == <целоеЧисло> - указанное число символов в строке;
   * - != <целоеЧисло> - любое число символов в строке, за исключением указанного;
   * - eq <строка> - эквивалентность строке;
   * - !eq <строка> - не эквивалентность строке;
   * - # <регулярноеВыражение> - соответствие регулярному выражению;
   * - > <целоеЧисло> - более чем указанное число символов в строке;
   * - >= <целоеЧисло> - не менее чем указанное число символов в строке;
   * - < <целоеЧисло> - менее чем указанное число символов в строке;
   * - <= <целоеЧисло> - не более чем указанное число символов в строке;
   * - [] <целоеЧисло> <целоеЧисло> - от указанного до указанного числа символов в строке включительно;
   * - () <целоеЧисло> <целоеЧисло> - от указанного до указанного числа символов в строке.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получение недопустимого значения второго аргумента.
   * @return boolean true - если верификация пройдена, иначе - false.
   */
  public function verify($mask){
    exceptions\InvalidArgumentException::verifyType($mask, 'S');

    $options = explode(' ', $mask);
    $typeVerify = array_shift($options);
    $count = $this->count();

    switch($typeVerify){
      case '==':
        if($count != $options[0]){
          return false;
        }
        break;
      case '!=':
        if($count == $options[0]){
          return false;
        }
        break;
      case 'eq':
        if($this->getVal() != implode(' ', $options)){
          return false;
        }
        break;
      case '!eq':
        if($this->getVal() == implode(' ', $options)){
          return false;
        }
        break;
      case '>':
        if($count <= $options[0]){
          return false;
        }
        break;
      case '>=':
        if($count < $options[0]){
          return false;
        }
        break;
      case '<':
        if($count >= $options[0]){
          return false;
        }
        break;
      case '<=':
        if($count > $options[0]){
          return false;
        }
        break;
      case '[]':
        if(!($count >= $options[0] && $count <= $options[1])){
          return false;
        }
        break;
      case '()':
        if(!($count > $options[0] && $count < $options[1])){
          return false;
        }
        break;
      case '#':
        $pattern = preg_replace('/([\\\#\/\]\[])/', '\\\${1}', implode(' ', $options));
        if(!((boolean) $this->match('/^[' . $pattern . ']*$/u'))){
          return false;
        }
        break;
      default:
        throw exceptions\InvalidArgumentException::getValidException('=|!=|eq|!eq|#|>|>=|<|<=|[]|()', $typeVerify);
    }

    return true;
  }

  /**
   * Метод удаляет из строки запрещенные символы и приводит ее к указанной длине.
   *
   * @param integer $minLength Минимальная длина строки в байтах. Если строка меньше, она дополняется слева пробелами.
   * @param integer $maxLength Максимальная длина строки в байтах. Если строка больше, она обрезается.
   * @param string $illegalChars [optional] Шаблон запрещенных символов, удаляемых из строки. Шаблон соответствует регулярному выражению для конструкции [].
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Результирующая строка.
   */
  public function prevent($minLength, $maxLength, $illegalChars = ''){
    exceptions\InvalidArgumentException::verifyType($minLength, 'i');
    exceptions\InvalidArgumentException::verifyVal($minLength, 'i >= 0');
    exceptions\InvalidArgumentException::verifyType($maxLength, 'i');
    exceptions\InvalidArgumentException::verifyVal($maxLength, 'i >= 0');
    exceptions\InvalidArgumentException::verifyType($illegalChars, 's');

    $count = $this->count();
    if($count < $minLength){
      $val = $this->pad($minLength)->getVal();
    }
    elseif($count > $maxLength){
      $val = $this->subLeft($maxLength)->getVal();
    }
    else{
      $val = $this->getVal();
    }

    if(empty($illegalChars)){
      return new static($val);
    }
    $illegalChars = preg_replace('/([\\\#\/\]\[])/', '\\\${1}', $illegalChars);
    return new static(preg_replace('/[' . $illegalChars . ']+/u', '', $val));
  }

  /**
   * Метод изменяет позицию внутреннего указателя строки на заданную.
   *
   * @param integer $point Новая позиция внутреннего указателя строки.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   */
  public function setPoint($point){
    exceptions\InvalidArgumentException::verifyType($point, 'i');
    exceptions\InvalidArgumentException::verifyVal($point, 'i [] 0 '.$this->length());

    $this->point = $point;
  }

  /**
   * Метод возвращает текущую позицию внутреннего указателя строки.
   * @return integer Текущая позиция внутреннего указателя строки.
   */
  public function getPoint(){
    return $this->point;
  }

  /**
   * Метод возвращает следующий компонент от текущей позиции указателя компонента до указанного ограничителя.
   *
   * @param string $delimiter Ограничитель компонента.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   * @return boolean|static Компонент или false - если указанный ограничитель не найден.
   */
  public function nextComponent($delimiter){
    exceptions\InvalidArgumentException::verifyType($delimiter, 'S');

    $positionDelimiter = strpos($this->val, $delimiter, $this->point);
    if($positionDelimiter < 0 || $positionDelimiter === false){
      return false;
    }
    $component = substr($this->val, $this->point, $positionDelimiter-$this->point);
    $this->point += strlen($component)+strlen($delimiter);
    return new static($component);
  }
}
