<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для предоставления дополнительной логики массивам.
 * Допустимый тип: тип данных array; при получении данных других типов, они приводятся к типу array становясь первым и единственным элементом массива.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class Arr extends Wrapper implements \ArrayAccess, \Iterator{
  /**
   * @var boolean Валидность текущией позиции указателя.
   */
  protected $valid;

  /**
   * Метод должен вызываться при изменении количества элементов в массиве для проверки валидности текущей позиции внутреннего указателя.
   */
  protected function mod(){
    if($this->count() == 0){
      $this->valid = false;
    }
    else{
      $this->valid = true;
    }

    if(next($this->val) === false){
      $this->valid = false;
    }
    else{
      prev($this->val);
    }
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
    return new self([$string]);
  }

  /**
   * @param mixed $val Оборачиваемое значение.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($val){
    if(!is_array($val)){
      throw exceptions\InvalidArgumentException::getTypeException('array', gettype($val));
    }
    parent::__construct($val);
    reset($this->val);
    $this->mod();
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
    return $this->val[$offset];
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   */
  public function offsetSet($offset, $value){
    $this->val[$offset] = $value;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   */
  public function offsetUnset($offset){
    unset($this->val[$offset]);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current(){
    return current($this->val);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next(){
    $this->valid = (next($this->val) !== false);
  }

  /**
   * Сдвигает внутренний указатель на одну позицию назад.
   * @return bool
   */
  public function prev(){
    prev($this->val);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return scalar scalar on success, or null on failure.
   */
  public function key(){
    return key($this->val);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   */
  public function valid(){
    return $this->valid;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind(){
    reset($this->val);
    $this->mod();
  }

  /**
   * Метод возвращает количество элементов данного массива.
   * @return integer Количество элементов массива.
   */
  public function count(){
    return count($this->getVal());
  }

  /**
   * Метод выталкивает первый элемент массива и возвращает его.
   * Данный метод сбрасывает внутренний указатель в массиве и числовая нумерация элементов массива станет такой, что элементы будут нумероваться с нуля.
   * @return mixed|null Извлеченный элемент массива или null - если массив пуст.
   */
  public function shift(){
    $val = array_shift($this->val);
    $this->mod();
    return $val;
  }

  /**
   * Метод вталкивает элемент в начало массива.
   * Числовая нумерация элементов массива станет такой, что элементы будут нумероваться с нуля.
   * @param mixed $val Вталкиваемый элемент.
   */
  public function unshift($val){
    array_unshift($this->val, $val);
    $this->mod();
  }

  /**
   * Метод выталкивает последний элемент массива и возвращает его.
   * Данный метод сбрасывает внутренний указатель в массиве.
   * @return mixed|null Извлеченный элемент массива или null - если массив пуст.
   */
  public function pop(){
    $val = array_pop($this->val);
    $this->mod();
    return $val;
  }

  /**
   * Метод вталкивает элемент в конец массива.
   * @param mixed $val Вталкиваемый элемент.
   */
  public function push($val){
    array_push($this->val, $val);
    $this->mod();
  }

  /**
   * Метод проверяет, присутствует ли указанный ключ в массиве.
   *
   * @param mixed $key Искомый ключ.
   *
   * @return boolean true - в случае успеха, иначе - false.
   */
  public function hasKey($key){
    return array_key_exists($key, $this->val);
  }

  /**
   * Метод выполняет поиск указанного значения в массиве и возвращает массив ключей, в которых содержиться данное значение.
   * @param mixed $val Искомое значение.
   * @param boolean $strict [optional] В случае указания true - поиск производится в соответствии со строгим равенством (===).
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Массив ключей, значения которых соответствуют искомому значению или пустой массив в случае отсутствия значения.
   */
  public function searchVal($val, $strict = false){
    exceptions\InvalidArgumentException::verifyType($strict, 'b');
    return new static(array_keys($this->val, $val, $strict));
  }

  /**
   * Метод возвращает срез массива с сохранением ключей.
   * @param integer $offset Индекс отступа начального указателя. Если значение положительное, отсчет производится с начала массива, иначе с конца.
   * @param integer $length [optional] Число отбираемых элементов. Если значение не задано, отбираются все элементы до конца массива.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Срез массива.
   */
  public function slice($offset, $length = null){
    exceptions\InvalidArgumentException::verifyType($offset, 'i');
    exceptions\InvalidArgumentException::verifyType($length, 'in');

    return new static(array_slice($this->val, $offset, $length, true));
  }

  /**
   * Метод вырезает указанный интервал элементов из массива.
   * Следует помнить, что числовые ключи массива сбрасываются.
   *
   * @param integer $offset Индекс отступа начального указателя. Если значение положительное, отсчет производится с начала массива, иначе с конца.
   * @param integer $length [optional] Число вырезаемых элементов. Если значение не задано, вырезаются все элементы до конца массива.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return static Массив удаленных элементов.
   */
  public function splice($offset, $length = null){
    exceptions\InvalidArgumentException::verifyType($offset, 'i');
    exceptions\InvalidArgumentException::verifyType($length, 'in');

    // Данное условие необходимо в связи с особым поведением функции array_splice. Данная функция при получении в качестве третьего параметра null или 0 не отбирает массив до конца, а отбирает 0 элементов возвращая всегда пустой массив
    if(is_null($length)){
      $val = new static(array_splice($this->val, $offset));
    }
    else{
      $val = new static(array_splice($this->val, $offset, $length));
    }
    $this->mod();
    return $val;
  }

  /**
   * Метод выполняет верификацию числа в соответствии с маской.
   *
   * @param string $mask Маска верификации
   * Аргумент имеет структуру: <типВалидации> <ключи валидации>.
   * Возможные значения аргумента:
   * - == <целоеЧисло> - указанное число элементов;
   * - != <целоеЧисло> - любое число элементов, за исключением указанного;
   * - > <целоеЧисло> - более чем указанное число элементов;
   * - >= <целоеЧисло> - не менее чем указанное число элементов;
   * - < <целоеЧисло> - менее чем указанное число элементов;
   * - <= <целоеЧисло> - не более чем указанное число элементов;
   * - [] <целоеЧисло> <целоеЧисло> - от указанного до указанного числа элементов включительно;
   * - () <целоеЧисло> <целоеЧисло> - от указанного до указанного числа элементов.
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
      default:
        throw exceptions\InvalidArgumentException::getValidException('==|!=|>|>=|<|<=|[]|()', $typeVerify);
    }

    return true;
  }
}