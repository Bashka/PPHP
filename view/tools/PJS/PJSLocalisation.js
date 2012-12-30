YUI().use('node', 'io-base', 'json-parse', 'json-stringify', function(Y){
  window.PJS = window.PJS || {};
  /*
   * Объектное представление локализатора сообщений уровня представления.
   * @public
   * @type {Object}
   */
  window.PJS.localisation = (function(){
    /*
     * Буфер файлов локализации.
     * @private
     * @type {String[]}
     */
    var localiseFiles = [],
    /*
     * Текущая локализация.
     * @private
     * @type {String}
     */
      localise;

    return {
      /*
       * Метод позволяет локализовать переданное сообщение.
       * @public
       * @function
       * @param {String} module Модуль, к которому относится сообщение.
       * @param {String} screen Экран, к которому относится сообщение.
       * @param {String} message Локализуемое сообщение.
       * @return {String} Локализованное сообщение или начальное сообщение, если локализация невозможна.
       */
      localiseMessage:function(module, screen, message){
        if(!localiseFiles[module]){
          localiseFiles[module] = [];
        }
        if(!localiseFiles[module][screen]){
          localiseFiles[module][screen] = [];
          Y.io('/PPHP/view/screens/' + module + '/' + screen + '/' + screen + '_' + localise + '.localise', {
            method:'GET',
            timeout:2000,
            sync:true,
            headers:{
              'Content-Type': 'text/html;charset=utf-8'
            },
            on:{
              success:function(code, xhr){
                var rows = xhr.responseText.split("\n");
                for(var i in rows){
                  var element = rows[i].split('=');
                  localiseFiles[module][screen][element[0]] = element[1];
                }
              }
            }
          });
        }
        if(localiseFiles[module][screen][message]){
          return localiseFiles[module][screen][message];
        }
        else{
          return message;
        }
      },

      /*
       * Метод позволяет определить текущую локализацию.
       * @public
       * @function
       * @param {String} lang Устанавливая локализация.
       */
      setLanguage:function(lang){
        localise = lang;
      }
    }
  })();
});