<?php
namespace PPHP\model\modules\System;

class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод выполняет upgrade системы из локального архива.
   * @param string $addressArchive Адрес архива.
   * @return boolean true - если upgrade успешно выполнен.
   */
  public function updateSystem($addressArchive){
    return Updater::getInstance()->update($addressArchive);
  }

  /**
   * Метод выполняет upgrade системы из удаленного архива.
   * @param string $urlArchive URL адрес архива upgrade.
   * @return boolean true - если upgrade выполнен успешно.
   */
  public function updateSystemURL($urlArchive){
    return Updater::getInstance()->updateURL($urlArchive);
  }
}

