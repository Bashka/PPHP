<?php
namespace PPHP\model\modules;

/**
 * Выбрасывается в случае, если производится попытка доступа к закрытому для данного пользователя методу.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules
 */
class AccessException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
}
