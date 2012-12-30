<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Данное исключение свидетельствует о том, что выполнение метода приведет к созданию двух компонентов с одинаковым именем в контексте одного каталога.
 */
class ComponentDuplicationException extends \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException{
}
