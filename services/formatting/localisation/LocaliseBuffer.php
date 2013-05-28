<?php
namespace PPHP\services\formatting\localisation;

use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\patterns\buffer\MapBuffer;

/**
 * Класс отвечает за буферизацию файлов локализаций.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\formatting\localisation
 */
class LocaliseBuffer extends MapBuffer{
  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * @param string $key
   * @param array|null $arguments
   * @throws NotFoundDataException Выбрасывается в случае, если требуемого файла локализации не существует
   * @return array Ассоциативный массив данных локализации
   */
  protected function getFromSource($key, array $arguments = null){
    if(!file_exists($key)){
      throw new NotFoundDataException('Отсутствует файл локализации ['.$key.'].');
    }
    return parse_ini_file($key, true);
  }
}
