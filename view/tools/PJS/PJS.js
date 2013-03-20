YUI().use('node', 'io-base', 'json-parse', 'json-stringify', function(Y){
  window.PJS = window.PJS || {};
  /**
   * Объектное представление ядра системы управления пользовательским интерфейсом.
   * @public
   * @type {Object}
   */
  window.PJS.core = function(){
    /**
     * Объектное представления индикатора загрузки.
     * @private
     * @type {Object}
     */
    var loading = (function(){
      /**
       * Количество активных запросов.
       * @private
       * @type {Number}
       */
      var countQuery = 0,
        /**
         * Узел индикатора загрузки.
         * @private
         * @type {Node}
         */
        loadingImg = Y.Node.create('<img src="/PPHP/view/tools/PJS/loading.gif" alt="Loading" style="position: absolute; top: 10px; left: 10px">');
      loadingImg.hide();

      return {
        /**
         * Метод добавляет новый запрос в очередь индикатора загрузки.
         * @public
         * @function
         */
        load:function(){
          if(countQuery == 0){
            loadingImg.show();
            Y.one('body').append(loadingImg);
          }
          countQuery++;
        },

        /**
         * Метод удаляет один запрос из очереди индикатора загрузки.
         * @public
         * @function
         */
        complete:function(){
          countQuery--;
          if(countQuery == 0){
            loadingImg.hide();
          }
        }
      }
    })();

    return {
      /*
       * Метод выполняет запрос к заданному модулю и передает ему данные.
       * @param {String} moduleName Имя запрашиваемого модуля.
       * @param {String} active Имя запрашиваемого метода модуля.
       * @param {Object} [options] Конфигурация запроса.
       */
      query:function(moduleName, active, options){
        var context = options.context || this,
          callback = options.callback || function(){
          },
          error = options.error || function(){
          },
          data = options.data,
          timeout = options.timeout || 5000;

        loading.load();

        var body = {
          module:moduleName,
          active:active
        };
        if(data !== undefined){
          body.message = Y.JSON.stringify(data);
        }
        Y.io('/PPHP/model/modules/CentralController.php', {
          method:((data === undefined)? 'GET' : 'POST'),
          data:body,
          timeout:timeout,
          on:{
            success:function(code, xhr){
              loading.complete();
              if(xhr.responseText != ''){
                var data = Y.JSON.parse(xhr.responseText);
                if(data.answer !== undefined){
                  callback.apply(context, [data.answer]);
                }
                else if(data.exception !== undefined){
                  PJS.log.addException(data.exception);
                  error.apply(context, [data.exception]);
                }
              }
            },
            failure:function(code, xhr){
              loading.complete();
              PJS.log.addException({
                type:'QueryError [' + xhr.status + ':' + xhr.statusText + ']',
                message:'Ошибка парсинга ответа. <br />' + xhr.responseText,
                code:1,
                file:'undefined',
                line:'undefined',
                trace:[]
              });
            }
          }
        });
      }
    }
  }();
});