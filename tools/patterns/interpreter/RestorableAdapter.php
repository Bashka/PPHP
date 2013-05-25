<?php
namespace PPHP\tools\patterns\interpreter;

/**
 * Данный класс может быть использован как родительский для классов, которые реализуют интерфейс Restorable через trait TRestorable без дополнительной логики.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
abstract class RestorableAdapter implements Restorable{
use TRestorable;
}