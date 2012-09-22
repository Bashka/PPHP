<?php
namespace PPHP\services\modules;

/**
 * Интерфейс управления роутингом модулей
 */
interface ModulesRouterInterface{
  /**
   * Метод возвращает контроллер для данного модуля
   * @abstract
   * @param string $moduleName Имя модуля
   * @return string
   */
  public function getController($moduleName);

  /**
   * Метод определяет, определен ли данный модуль в системе
   * @abstract
   * @param string $moduleName Имя модуля
   * @return boolean true - если модуль установлен, иначе - false
   */
  public function isModuleExists($moduleName);

  /**
   * Метод добавляет новой путь в роутер
   * @abstract
   * @param string $moduleName Имя модуля
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller Отображение класса контроллера для данного модуля
   */
  public function setController($moduleName, \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller);

  /**
   * Метод удаляет данные модуля из роутера
   * @abstract
   * @param string $moduleName Имя модуля
   * @return boolean true - если модуль был успешно удален из роутера, иначе - false
   */
  public function removeController($moduleName);
}
