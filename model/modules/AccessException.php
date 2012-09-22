<?php
namespace PPHP\model\modules;

/**
 * Выбрасывается в случае, если производится попытка доступа к закрытому для данного пользователя методу.
 */
class AccessException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
}
