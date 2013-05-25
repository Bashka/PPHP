<?php
namespace PPHP\tools\classes\standard\fileSystem\loadingFiles;
use \PPHP\tools\classes\standard\fileSystem as fileSystem;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет загруженный клиентом файл.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
class LoadedFile extends fileSystem\File{
  /**
   * MIME-тип файла.
   * @var string
   */
  protected $mimeType;

  /**
   * Метод получает файл из временного хранилища.
   * @static
   * @param string $fileName Псевдоним получаемого файла.
   *
   * @throws fileSystem\NotExistsException Выбрасывается в случае, если заданного файла не существует.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return LoadedFile Загруженный файл.
   */
  public static function getLoadedFile($fileName){
    exceptions\InvalidArgumentException::verifyType($fileName, 'S');

    if($_FILES[$fileName]['error'] == 0 && file_exists($_FILES[$fileName]['tmp_name'])){
      // Перехват исключений не выполняется в связи с невозможностью их появления
      $tempDir = fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tools/classes/standard/fileSystem/loadingFiles/temp');

      copy($_FILES[$fileName]['tmp_name'], $tempDir->getAddress().'/'.$_FILES[$fileName]['name']);
      $loadedFile = new static($_FILES[$fileName]['name'], $tempDir);
      $loadedFile->mimeType = $_FILES[$fileName]['type'];
      return $loadedFile;
    }
    else{
      throw new fileSystem\NotExistsException('Требуемого компонента файловой системы не найдено.');
    }
  }

  /**
   * @return string
   */
  public function getMimeType(){
    return $this->mimeType;
  }
}