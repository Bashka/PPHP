<?php
namespace PPHP\tools\classes\standard\fileSystem\loadingFiles;

/**
 * Класс представляет загруженный клиентом файл.
 */
class LoadedFile extends \PPHP\tools\classes\standard\fileSystem\File{
  /**
   * MIME-тип файла.
   * @var string
   */
  protected $mimeType;

  /**
   * Метод получает файл из временного хранилища.
   * @static
   * @param string $fileName Псевдоним получаемого файла.
   * @return LoadedFile Загруженный файл.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если заданного файла не существует.
   */
  public static function getLoadedFile($fileName){
    if($_FILES[$fileName]['error'] == 0 && file_exists($_FILES[$fileName]['tmp_name'])){
      $tempDir = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tools/classes/standard/fileSystem/loadingFiles/temp');
      copy($_FILES[$fileName]['tmp_name'], $tempDir->getAddress().'/'.$_FILES[$fileName]['name']);
      $loadedFile = new static($_FILES[$fileName]['name'], $tempDir);
      $loadedFile->mimeType = $_FILES[$fileName]['type'];
      return $loadedFile;
    }
    else{
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException;
    }
  }

  /**
   * @return mixed
   */
  public function getMimeType(){
    return $this->mimeType;
  }
}