<?php
namespace PPHP\services\formatting\localisation;

/**
 * Класс отвечает за буферизацию файлов локализаций
 */
class LocaliseBuffer extends \PPHP\tools\patterns\buffer\MapBuffer{
  protected $iniFile;

  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * @param string $key
   * @param array|null $arguments
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return array Ассоциативный массив данных локализации
   */
  protected function getFromSource($key, array $arguments = null){
    if(!file_exists($key)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    return parse_ini_file($key, true);
  }

  /**
   * Метод возвращает содержимое файла локализации по его адресу
   * @param string $fileAddress Адрес файла локализации
   * @return array Ассоциативный массив данных локализации
   */
  public function getLocaliseData($fileAddress){
    return $this->getData($fileAddress);
  }
}
