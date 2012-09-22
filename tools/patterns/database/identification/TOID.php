<?php
namespace PPHP\tools\patterns\database\identification;

trait TOID{
  protected $OID;

  /**
   * Метод возвращает идентификатор объекта.
   * @return integer
   */
  public function getOID(){
    return $this->OID;
  }

  /**
   * Метод устанавливает идентификатор нового объекта.
   * @param integer $OID
   * @throws UpdatingOIDException Выбрасывается при попытке изменения идентификатора.
   * @return void
   */
  public function setOID($OID){
    if(!empty($this->OID)){
      throw new UpdatingOIDException();
    }
    else{
      $this->OID = $OID;
    }
  }

  /**
   * Метод проверяет, определен ли идентификатор для объекта.
   * @abstract
   * @return boolean true - если идентификатор определен, иначе - false.
   */
  public function isOID(){
    return !is_null($this->getOID());
  }

  /**
   * Метод возвращает ссылку на объект в виде строки.
   * @abstract
   * @return string Ссылка формата $ИмяКласса:OID
   */
  public function getLinkOID(){
    return '$/' . str_replace('\\', '/', get_class($this)) . ':' . $this->getOID();
  }

  /**
   * Метод возвращает фиктивный объект, готовый к восстановлению по OID.
   * @static
   * @abstract
   * @param integer $OID Идентификатор объекта.
   * @return static
   */
  public static function getProxy($OID){
    $proxy = new static;
    $proxy->setOID($OID);
    return $proxy;
  }
}
