<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Определяет интерфейс доступа к хранилищам на основе реляционных таблиц.
 */
interface Storage{
  /**
   * Метод считывает заданную строку данных и возвращает ее объектное представление. Если данной записи не существует или она была предворительно удалена, метод вернет null.
   * @param integer $id Номер записи.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return array|null Массив данных или null, если заданная запись была стерта.
   */
  public function select($id);

  /**
   * Метод изменяет значение записи на заданное.
   * @param integer $id Индекс записи.
   * @param array $data Данные, на которые должена быть заменена запись.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function update($id, array $data);

  /**
   * Метод стирает указанную запись.
   * @param integer $id Индекс записи.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function delete($id);

  /**
   * Метод вставляет новую запись в конец таблицы.
   * @param array $data Новая, вставляемая запись.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function insert(array $data);

  /**
   * Метод возвращает текущее количество записей в таблице с учетом стертых записей.
   * @return integer Текущее количество записей.
   */
  public function getCountRecord();
}
