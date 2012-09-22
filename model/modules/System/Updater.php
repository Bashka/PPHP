<?php
namespace PPHP\model\modules\System;

/**
 * Класс отвечает за upgrade системы.
 */
class Updater implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод формирует массив данных для upgrade.
   * @param string $archiveAddress Адрес архива.
   * @return array Массив данных для upgrade.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если архив не имеет требуемых для upgrade компонентов.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если для upgrade недостаточно данных.
   */
  protected function getDataUpdate($archiveAddress){
    if(!file_exists($archiveAddress)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Требуемый архив для upgrade не найден.');
    }
    $data = [];
    $zip = new \ZipArchive;
    $data['archive'] = $zip;

    $zip->open($archiveAddress);
    if(!($zip->statName('map.xml'))){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Нарушена структура арихва.');
    }

    // Получение карты апгрейта
    $zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp', ['map.xml']);

    // Парсинг карты апгейта
    $map = new \DOMDocument('1.0', 'UTF-8');
    $map->preserveWhiteSpace = false;
    $map->load($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp/map.xml');

    // Определение шага
    $version = $map->getElementsByTagName('step')->item(0);
    if(!$version->hasAttribute('from') || !$version->hasAttribute('to')){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Недостаточно данных для приведения версий.');
    }
    $data['from'] = trim($version->getAttribute('from'));
    $data['to'] = trim($version->getAttribute('to'));

    $body = $map->getElementsByTagName('body')->item(0);

    // Построение массива создаваемых компонентов
    $creates = $body->getElementsByTagName('create');
    $data['creates'] = ['file' => [], 'dir' => [], 'count' => 0];
    foreach($creates as $v){
      $data['creates']['count']++;
      $attr = ($v->hasAttribute('file'))? 'file' : 'dir';
      $component = $v->getAttribute($attr);
      $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $v->firstChild->data;
      $data['creates'][$attr][] = ['component' => $component, 'path' => $path];
    }

    // Построение массива обновляемых компонентов
    $updates = $body->getElementsByTagName('update');
    $data['updates'] = ['file' => [], 'dir' => [], 'count' => 0];
    foreach($updates as $v){
      $data['updates']['count']++;
      $attr = ($v->hasAttribute('file'))? 'file' : 'dir';
      $component = $v->getAttribute($attr);
      $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $v->firstChild->data;
      $data['updates'][$attr][] = ['component' => $component, 'path' => $path];
    }

    // Построение массива удаляемых компонентов
    $deletes = $body->getElementsByTagName('delete');
    $data['deletes'] = ['file' => [], 'dir' => [], 'count' => 0];
    foreach($deletes as $v){
      $data['deletes']['count']++;
      $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $v->firstChild->data;
      $attr = (strrpos($path, '.') === false)? 'dir' : 'file';
      $data['deletes'][$attr][] = $path;
    }

    // Удаление временных файлов
    \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp/map.xml')->delete();

    return $data;
  }

  /**
   * Метод выполняет upgrade системы из локального архива.
   * @param string $archiveAddress Адрес архива.
   * @return boolean true - если upgrade успешно выполнен.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если upgrade невозможнен в связи с версией платформы.
   */
  public function update($archiveAddress){
    $data = $this->getDataUpdate($archiveAddress);
    if(\PPHP\services\configuration\Configurator::getInstance()->get('System', 'Version') != $data['from']){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\LogicException('Невозможно произвести upgrade. Версия платформы отличается от требуемой.');
    }

    // Получение компонентов архива
    $dirComponents = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp/components');
    $data['archive']->extractTo($dirComponents->getAddress());

    // Создание файла лога
    $log = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/log/' . $data['from'] . '_' . $data['to'] . '.txt');
    if(!$log->isExists()){
      $log->create();
    }
    $logWriter = $log->getWriter();
    $logWriter->clean();
    $logWriter->write(date('d.m.Y' . "\n", time()));

    // Создание компонентов
    if($data['creates']['count'] != 0){
      $logWriter->write('Create components:' . "\n");
      foreach($data['creates']['file'] as $fileData){
        $address = $fileData['path'] . '/' . $fileData['component'];
        if($dirComponents->isFileExists($fileData['component'])){
          $file = $dirComponents->getFile($fileData['component']);
          if(!file_exists($address) || !is_file($address)){
            $file->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($fileData['path']));
            $logWriter->write('Passed: ' . $address . "\n");
          }
          else{
            $logWriter->write('Failed: file exists ' . $address . "\n");
          }
        }
        else{
          $logWriter->write('Failed: component not found ' . $address . "\n");
        }
      }

      foreach($data['creates']['dir'] as $dirData){
        $address = $dirData['path'] . '/' . $dirData['component'];
        if($dirComponents->isDirExists($dirData['component'])){
          $dir = $dirComponents->getDir($dirData['component']);
          if(!file_exists($address) || !is_dir($address)){
            $dir->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($dirData['path']));
            $logWriter->write('Passed: ' . $address . "\n");
          }
          else{
            $logWriter->write('Failed: dir exists ' . $address . "\n");
          }
        }
        else{
          $logWriter->write('Failed: component not found ' . $address . "\n");
        }
      }
    }

    // Удаление компонентов
    if($data['deletes']['count'] != 0){
      $logWriter->write('Delete components:' . "\n");
      foreach($data['deletes']['file'] as $fileData){
        if(file_exists($fileData) && is_file($fileData)){
          $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($fileData);
          chmod($file->getAddress(), 0777);
          $file->delete();
          $logWriter->write('Passed: ' . $fileData . "\n");
        }
        else{
          $logWriter->write('Failed: file not found ' . $fileData . "\n");
        }
      }

      foreach($data['deletes']['dir'] as $dirData){
        if(file_exists($dirData) && is_dir($dirData)){
          $dir = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($dirData);
          chmod($dir->getAddress(), 0777);
          $dir->delete();
          $logWriter->write('Passed: ' . $dirData . "\n");
        }
        else{
          $logWriter->write('Failed: dir not found ' . $dirData . "\n");
        }
      }
    }

    // Обновление компонентов
    if($data['updates']['count'] != 0){
      $logWriter->write('Update components:' . "\n");
      foreach($data['updates']['file'] as $fileData){
        if($dirComponents->isFileExists($fileData['component'])){
          $file = $dirComponents->getFile($fileData['component']);
          if(file_exists($fileData['path']) && is_file($fileData['path'])){
            $deleted = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($fileData['path']);
            chmod($deleted->getAddress(), 0777);
            $deleted->delete();
            $file->move($deleted->getLocation());
            $logWriter->write('Passed: ' . $fileData['path'] . "\n");
          }
          else{
            $logWriter->write('Failed: file not found ' . $fileData['path'] . "\n");
          }
        }
        else{
          $logWriter->write('Failed: component not found ' . $fileData['path'] . "\n");
        }
      }

      foreach($data['updates']['dir'] as $dirData){
        if($dirComponents->isDirExists($dirData['component'])){
          $dir = $dirComponents->getDir($dirData['component']);
          if(file_exists($dirData['path']) && is_dir($dirData['path'])){
            $deleted = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($dirData['path']);
            chmod($deleted->getAddress(), 0777);
            $deleted->delete();
            $dir->move($deleted->getLocation());
            $logWriter->write('Passed: ' . $dirData['path'] . "\n");
          }
          else{
            $logWriter->write('Failed: dir not found ' . $dirData['path'] . "\n");
          }
        }
        else{
          $logWriter->write('Failed: component not found ' . $dirData['path'] . "\n");
        }
      }
    }

    // Изменение версии системы
    \PPHP\services\configuration\Configurator::getInstance()->set('System', 'Version', $data['to']);

    // Удаление временных файлов и архива
    $dirComponents->clear();
    $data['archive']->close();
    $archiveFile = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($archiveAddress);
    if($archiveFile->isExists()){
      $archiveFile->delete();
    }

    // Закрытие лога
    $logWriter->close();

    return true;
  }

  /**
   * Метод выполняет upgrade системы из удаленного архива.
   * @param string $urlArchive URL адрес архива upgrade.
   * @return boolean true - если upgrade выполнен успешно.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если невозможно загрузить архив upgrade из удаленного хранилища.
   * @throws \Exception
   */
  public function updateURL($urlArchive){
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlArchive));
    fclose($arch);
    if(!file_exists($address)){
      throw new \PPHP\tools\classes\standard\fileSystem\NotExistsException('Невозможно загрузить архив для upgrade.');
    }
    try{
      return $this->update($address);
    }
    catch(\Exception $exc){
      \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/System/temp/0')->delete();
      throw $exc;
    }
  }
}