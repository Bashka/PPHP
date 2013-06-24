<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

/**
 * Исключение, свидетельствующее о состоянии окружения, не позволяющем системе корректно продолжать работу (на пример удаление используемого файла или закрытие сетевого соединения).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\exceptions
 */
abstract class EnvironmentException extends RuntimeException{
}