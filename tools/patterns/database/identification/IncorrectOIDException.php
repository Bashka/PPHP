<?php
namespace PPHP\tools\patterns\database\identification;

/**
 * Данное исключение свидетельствует о том, что объект имеет идентификатор, когда это не требуется, или не имеет его, когда это требуется.
 */
class IncorrectOIDException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
}
