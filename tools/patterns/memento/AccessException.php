<?php
namespace PPHP\tools\patterns\memento;

/**
 * Данное исключение свидетельствует о том, что производится попытка доступа к закрытым свойствам объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\memento
 */
class AccessException extends \PPHP\tools\classes\standard\baseType\exceptions\LogicException{
}
