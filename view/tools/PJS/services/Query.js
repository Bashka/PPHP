/**
 * @namespace PPHP\view\tools\PJS\services\Query
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.services.Query', function(Y){
  /**
   * Данная служба позволяет передавать сообщения серверу и принимать ответы от него.
   */
  var Query = (function(){
    /**
     * Физический адрес центрального контроллера.
     * @private
     * @static
     * @type {string}
     */
    var CENTRAL_CONTROLLER = '/PPHP/model/modules/CentralController.php',
      /**
       * Ожидание ответа в миллисекундах.
       * @private
       * @static
       * @type {int}
       */
        TIMEOUT = 5000;

    /**
     * Функция-обертка для обработчика события завершения запроса.
     * @private
     * @function
     * @param {String} code Код ответа сервера.
     * @param {XMLHttpRequest} xhr Объект запроса.
     * @param {Object} callbacks Пользовательские функции-обработчики событий ответа сервера.
     */
    function _completeListener(code, xhr, callbacks){
      Query.fire('PJS.services.Query:QueryComplete', {
        xhr: xhr
      });
      if(callbacks.complete){
        callbacks.complete.apply(this, [xhr]);
      }
    }

    /**
     * Функция-обертка для обработчика события удачного завершения запроса.
     * @private
     * @function
     * @param {String} code Код ответа сервера.
     * @param {XMLHttpRequest} xhr Объект запроса.
     * @param {Object} callbacks Пользовательские функции-обработчики событий ответа сервера.
     */
    function _successListener(code, xhr, callbacks){
      if(xhr.responseText != ''){
        var data = Y.JSON.parse(xhr.responseText);
        if(data.answer !== undefined){
          Query.fire('PJS.services.Query:QuerySuccess', {
            answer: data.answer
          });
          if(callbacks.success){
            callbacks.success.apply(this, [data.answer]);
          }
        }
        else if(data.exception !== undefined){
          var error = new Error(data.exception.class+': '+data.exception.message+'. ['+data.exception.file+':'+data.exception.line+']');
          error.class = data.exception.class;
          error.file = data.exception.file;
          error.line = data.exception.line;
          error.buffer = data.exception.buffer;
          Query.fire('PJS.services.Query:QueryFailure', {
            exception: error
          });
          Y.log(error.message, "error");
          if(callbacks.failure){
            callbacks.failure.apply(this, [error]);
          }
        }
      }
    }

    /**
     * Функция-обертка для обработчика события неудачного завершения запроса.
     * @private
     * @function
     * @param {String} code Код ответа сервера.
     * @param {XMLHttpRequest} xhr Объект запроса.
     * @param {Object} callbacks Пользовательские функции-обработчики событий ответа сервера.
     */
    function _failureListener(code, xhr, callbacks){
      var error = new Error(xhr.responseText);
      Query.fire('PJS.services.Query:QueryFailure', {
        exception: error
      });
      Y.log(error, "error");
      if(callbacks.failure){
        callbacks.failure.apply(this, [error]);
      }
    }

    return {
      /**
       * Метод передает запрос центральному контроллеру.
       * Метод вызывает следующие события на объекте:
       * - PJS.services.Query:QueryStart - запрос отправлен.
       * - PJS.services.Query:QueryComplete - запрос завершен. Параметр события xhr хранит объект XMLHttpRequest;
       * - PJS.services.Query:QuerySuccess - запрос завершен успешно. Параметр события answer хранит ответ сервера;
       * - PJS.services.Query:QueryFailure - запрос завершен неуспешно. Параметр события exception хранит объект класса Error, описывающий исключение.
       * @public
       * @function
       * @param {String} module Имя целевого модуля.
       * @param {String} action Имя целевого метода.
       * @param {Object} options Объект конфигурации запроса. Объект может включать следующие свойства:
       * - data - передаваемый серверу массив данных;
       * - timeout - время ожидания ответа в миллисекундах;
       * - context - контекст исполнения функций-обработчиков;
       * - complete(xhr) - функция-обработчик заверешния запроса. Функция принимает единственный аргумент - объект XMLHttpRequest;
       * - success(answer) - функция-обработчик удачного заверешния запроса. Функция принимает единственный аргумент - ответ сервера;
       * - failure(error) - функция-обработчик неудачного заверешния запроса. Функция принимает единственный аргумент - ошибку.
       */
      query: function(module, action, options){
        var data = {
          module: module,
          active: action
        };
        if(options.data !== undefined){
          data.message = Y.JSON.stringify(options.data);
        }

        options.context = options.context || this;

        Query.fire('PJS.services.Query:QueryStart');

        Y.io(CENTRAL_CONTROLLER,
          {
            method: (data.message === undefined)? 'GET' : 'POST',
            data: data,
            timeout: (options.timeout === undefined)? TIMEOUT : options.timeout,
            context: options.context,
            arguments: {
              complete: options.complete,
              success: options.success,
              failure: options.failure
            },
            on: {
              complete: _completeListener,
              success: _successListener,
              failure: _failureListener
            }
          });
      }
    }
  })();

  Y.augment(Query, Y.EventTarget);
  Y.namespace('PJS.services').Query = Query;
}, '1.0', {requires: ['io-base', 'json-parse', 'json-stringify', 'event', 'node', 'oop']});

/*
 Y.PJS.services.Query.query('Console', 'test', {});
*/