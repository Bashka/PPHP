<?php
namespace PPHP\services;

/**
 * Выбрасывается в случае, если сервис не имеет данных для инициализации
 */
class InitializingDataNotFoundException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
}
