<?php
namespace PPHP\tools\classes\standard\fileSystem\loadingFiles;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\fileSystem as fileSystem;

/**
 * Класс представляет загруженный клиентом файл.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\loadingFiles
 */
class LoadedFile extends fileSystem\File{
  /**
   * @var string MIME-тип файла.
   */
  protected $mimeType;

  /**
   * Метод получает файл из временного хранилища.
   * @static
   * @param string $fileName Псевдоним получаемого файла.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если заданного файла не существует.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return \PPHP\tools\classes\standard\fileSystem\loadingFiles\LoadedFile Загруженный файл.
   */
  public static function getLoadedFile($fileName){
    InvalidArgumentException::verifyType($fileName, 'S');
    if($_FILES[$fileName]['error'] == 0 && file_exists($_FILES[$fileName]['tmp_name'])){
      // Перехват исключений не выполняется в связи с невозможностью их появления
      $tempDir = new fileSystem\Directory('PPHP/tools/classes/standard/fileSystem/loadingFiles/temp');
      if(!copy($_FILES[$fileName]['tmp_name'], $tempDir->getAddress() . '/' . $_FILES[$fileName]['name'])){
        throw new fileSystem\AccessException('Невозможно переместить файл [' . $fileName . '] в файловое хранилище [/PPHP/tools/classes/standard/fileSystem/loadingFiles/temp]. Возможно недостаточно прав для данной операции.');
      }
      $loadedFile = new static($_FILES[$fileName]['name'], $tempDir);
      $loadedFile->mimeType = $_FILES[$fileName]['type'];

      return $loadedFile;
    }
    else{
      throw new fileSystem\NotExistsException('Требуемого компонента файловой системы не найдено.');
    }
  }

  /**
   * Метод возвращает MIME тип файла.
   * @return string MIME тип файла.
   */
  public function getMimeType(){
    return $this->mimeType;
  }
}