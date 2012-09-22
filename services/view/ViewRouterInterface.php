<?php
namespace PPHP\services\view;

/**
 * Интерфейс управления роутингом экранов.
 */
interface ViewRouterInterface{
  /**
   * Метод возвращает заданный экран данного модуля.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return string Адрес экрана
   */
  public function getScreen($moduleName, $screenName);

  /**
   * @abstract
   * Метод определяет, задан ли данный экран.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return boolean true - если экран определен, иначе - false.
   */
  public function isScreenExists($moduleName, $screenName);

  /**
   * @abstract
   * Метод задает экран модулю.
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @param string $screen Расположение экрана.
   */
  public function setScreen($moduleName, $screenName, $screen);

  /**
   * Метод удаляет экран из роутинга.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @param string $screenName Имя экрана.
   * @return boolean true - если экран успешно удален из роутинга, иначе - false.
   */
  public function removeScreen($moduleName, $screenName);
}
