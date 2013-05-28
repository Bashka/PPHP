<?php
namespace PPHP\tests\tools\classes\standard\storage\database;
use PPHP\tools\classes\standard\storage\database\PDO;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';

class PDOTest extends \PHPUnit_Framework_TestCase{
  /**
   * Имя драйвера.
   */
  const dbDriver = 'mysql';
  /**
   * Адрес сервера с БД.
   */
  const host = 'localhost';
  /**
   * Имя БД.
   */
  const dbName = 'test';
  /**
   * Пользователь БД.
   */
  const user = 'root';
  /**
   * Пароль пользователя БД.
   */
  const password = 'root';
  /**
   * @var PDO
   */
  protected $object;

  protected function setUp(){
    $this->object = new PDO(self::dbDriver . ':host=' . self::host . ';dbname=' . self::dbName, self::user, self::password);
  }

  /**
   * @covers PDO::query
   */
  public function testQuery(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\PDOException');
    $this->object->query('SELECT * FROM `1`');
  }
}
