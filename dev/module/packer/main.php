<?php
namespace PPHP\dev\module\packer;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';

/**
 * Функция рекурсивно добавляет каталог в архив.
 * @param \ZipArchive $zip Принимающий архив.
 * @param string $dir Полный адрес добавляемого каталога.
 * @param string $base Полный адрес каталога, содержащего добавляемый каталог.
 * @return \ZipArchive Принимающий архив.
 */
function addDirectoryToZip($zip, $dir, $base){
  $newFolder = str_replace($base, '', $dir);
  $zip->addEmptyDir($newFolder);
  foreach(glob($dir . '/*') as $file)
  {
    if(is_dir($file))
    {
      $zip = addDirectoryToZip($zip, $file, $base);
    }
    else
    {
      $newFile = str_replace($base, '', $file);
      $zip->addFile($file, $newFile);
    }
  }
  return $zip;
}

/**
 * @var string $module Имя упаковываемого модуля.
 */
$module = 'InstallerModules';
/**
 * @var string Каталог файловой системы, служащий средством хранения архивов модулей. Данный каталог должен иметь достаточные для записи в него права. Параметр используется только при выполнении задачи Move in archive.
 */
$archiveAddress = '/home/artur/sync/dev/Delphinum/modules';
/*
Допустимыми значениями свойства $task являются:
- 1 (Structure control) - инструмент проверяет готовность модуля к упаковке;
- 2 (Create src) - инструмент готовит модуль к упаковке создавая файл конфигурации и удаляя дочерние модули;
- 3 (Create bin) - инструмент упаковывает модуль;
- 4 (Move in archive) - инструмент переносит упакованный модуль в хранилище.
 */
/**
 * @var integer $task Текущая задача инструмента.
 */
$task = 4;

// Проверка структуры.
if($task > 0){
  echo 'Проверка структуры модуля.'."\n";
  /**
   * @var \PPHP\services\modules\ModulesRouter $mr Роутер модулей.
   */
  $mr = ModulesRouter::getInstance();
  if(!$mr->hasModule($module)){
    throw new ModuleNotFoundException('Целевой модуль ['.$module.'] не найден в файле роутинга.');
  }
  $moduleDir = new Directory(ModulesRouter::MODULES_DIR.'/'.$mr->getModule($module));
  if(!$moduleDir->isExists()){
    throw new NotExistsException('Каталог целевого модуля ['.$module.'] не найден в файловой системе.');
  }
  if(!$moduleDir->isFileExists('state.ini')){
    throw new NotExistsException('Не найден файл состояния модуля ['.$module.'].');
  }
  $conf = new FileINI($moduleDir->getFile('state.ini'), true);
  if(!$conf->isDataExists('name', 'Component')){
    throw new NotFoundDataException('В файле состояния модуля ['.$module.'] отсутствует обязательный элемент [Component::name]');
  }
  if(!$conf->isDataExists('version', 'Component')){
    throw new NotFoundDataException('В файле состояния модуля ['.$module.'] отсутствует обязательный элемент [Component::version]');
  }
  if(!$conf->isDataExists('type', 'Component')){
    throw new NotFoundDataException('В файле состояния модуля ['.$module.'] отсутствует обязательный элемент [Component::type]');
  }
  $moduleType = $conf->get('type', 'Component');
  if($moduleType != ReflectionModule::SPECIFIC && $moduleType != ReflectionModule::VIRTUAL){
    throw new StructureException('Недопустимое значение свойства [Component::type]. Ожидается ['.ReflectionModule::SPECIFIC.' или '.ReflectionModule::VIRTUAL.'] вместо ['.$moduleType.'].');
  }
  if($moduleType == ReflectionModule::SPECIFIC && !$moduleDir->isFileExists('Controller.php')){
    throw new NotExistsException('Файл контроллера модуля ['.$module.'] не найден.');
  }
  echo 'Проверка структуры завершена.'."\n";
}
// Создание каталога исходного кода.
if($task > 1){
  echo 'Создание каталога исходного кода модуля.'."\n";
  $packerDir = new Directory('PPHP/dev/module/packer');
  $moduleDir = $moduleDir->copyPaste($packerDir);
  $conf = $moduleDir->getFile('state.ini');
  $conf->rename('conf.ini');
  $conf = new FileINI($conf, true);
  $children = explode(',', $conf->get('children', 'Depending'));
  $conf->remove('destitute', 'Depending');
  $conf->remove('children', 'Depending');
  $conf->rewrite();
  foreach($children as $child){
    if(!empty($child) && $moduleDir->isDirExists($child)){
        $moduleDir->getDir($child)->delete();
      }
  }
  echo 'Создание каталога завершено.'."\n";
}
// Упаковка каталога исходного кода.
if($task > 2){
  echo 'Упаковка модуля.'."\n";
  $components = $moduleDir->getDirectoryIterator();
  $zip = new \ZipArchive();
  $zipName = $module.'_'.$conf->get('version', 'Component').'.zip';
  $zipAddress = $packerDir->getAddress().'/'.$zipName;
  $zip->open($zipAddress, \ZipArchive::CREATE);
  /**
   * @var \DirectoryIterator $component
   */
  foreach($components as $component){
    if($component->getFilename() != '.' && $component->getFilename() != '..'){
      if($component->isFile()){
        $zip->addFile($component->getPathname(), $component->getFilename());
      }
      else{
        addDirectoryToZip($zip, $component->getPathname(), $moduleDir->getAddress());
      }
    }
  }
  $zip->close();
  $moduleDir->delete();
  echo 'Упаковка модуля завершена.'."\n";
}
// Перемещение архива модуля в хранилище.
if($task > 3){
  echo 'Перемещение в хранилище.'."\n";
  // Перемещение архива.
  $srcDir = $archiveAddress.'/src/'.$module;
  if(!file_exists($srcDir) || !is_dir($srcDir)){
    mkdir($srcDir);
  }
  if(file_exists($srcDir.'/'.$zipName)){
    unlink($srcDir.'/'.$zipName);
  }
  rename($zipAddress, $srcDir.'/'.$zipName);
  echo 'Перемещение завершено.'."\n";
}

