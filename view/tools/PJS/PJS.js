/*
 * Ядро системы управления пользовательским интерфейсом.
 */
var PJS = function(){
  /*
   * Метод оборачивает заданную функцию обратного вызова, используемую при выполнении метода query, в анонимную функцию, служащую для распаковки ответа модуля.
   * @param function callback Упаковываемая функция.
   */
  var wrapForQuery = function(callback, error, context){
    var wrap = function(data, code){
      if(data.answer !== undefined){
        var context = arguments.callee.context || this
        arguments.callee.callback.apply(context, [data.answer]);
      }
      else if(data.exception !== undefined){
        PJS.log.addException(data.exception);
        arguments.callee.error(data.exception);
      }
    }
    wrap.callback = callback;
    wrap.error = error;
    wrap.context = context;
    return wrap;
  }

  return {
    /*
     * Контейнер, содержащий контроллеры используемых в системе экранов.
     * @var Object
     */
    controllers:{},

    /*
     * Метод выполняет запрос к заданному модулю и передает ему данные.
     * @param string moduleName Имя запрашиваемого модуля.
     * @param string active Имя запрашиваемого метода модуля.
     * @param Object [optional] data Данные, передаваемые модулю. Если данные не передаются, то в качестве данного аргумента можно передать функцию обратного вызова.
     * @param function [optional] callback Функция обратного вызова, вызываемая при получении ответа от модуля. Данной функции передается один параметр - ответ модуля.
     * @param function [optional] error Функция обратного вызова, вызываемая при ошибочном запросе. Данной функции передается три параметра: код ошибки, текстовое представление ошибки и сообщение об ошибке.
     * @param function [optional] complete Функция обратного вызова, вызываемая при получении ответа от модуля.
     * @param object [optional] context Контекста вызова функции callback.
     */
    query      :function(moduleName, active, data, callback, error, complete, context){
      if(typeof data == 'function'){
        if(typeof complete == 'object'){
          context = complete;
        }
        if(typeof error == 'object'){
          context = error;
        }
        if(typeof callback == 'object'){
          context = callback;
        }
        complete = error || function(){};
        error = callback || function(){};
        callback = data;
        data = undefined;
      }
      else{
        callback = callback || function(){
        }
        error = error || function(){
        }
        complete = complete || function(){
        }
      }

      jQuery.ajax({
        url     :'/PPHP/model/modules/CentralController.php',
        type    :((data === undefined)? 'GET' : 'POST'),
        data    :{
          module :moduleName,
          active :active,
          message:data
        },
        dataType:'json',
        success :wrapForQuery(callback, error, context),
        error   :function(a){
          PJS.log.addException({
            type   :'QueryError',
            message:'Ошибка парсинга ответа. <br />' + a.responseText,
            code   :1,
            file   :'undefined',
            line   :'undefined',
            trace  :[]
          });
        },
        complete: complete
      });
    },

    /*
     * Метод предоставляет более удобный и короткий доступ к локализатору сообщений.
     * @param object controller Контроллер экрана, сообщение которого локализуется.
     * @param string message Локализуемое сообщение.
     * @return string Локализованное сообщение или начальное сообщение, если локализация невозможна.
     */
    locMess    :function(controller, message){
      if(this.localisation){
        return this.localisation.localiseMessage(controller.module, controller.screen, message)
      }
      else{
        return message;
      }
    }
  }
}();