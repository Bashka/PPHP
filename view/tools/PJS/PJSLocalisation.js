var PJS = PJS || {};
/*
 * Локализатор сообщений.
 */
PJS.localisation = function(){
  /*
   * Буфер файлов локализации.
   * @var array
   */
  var localiseFiles = [],
  /*
   * Текущая локализация.
   * @var string
   */
  localise;

  return {
    /*
     * Метод позволяет локализовать переданное сообщение.
     * @param string module Модуль, к которому относится сообщение.
     * @param string screen Экран, к которому относится сообщение.
     * @param string message Локализуемое сообщение.
     * @return string Локализованное сообщение или начальное сообщение, если локализация невозможна.
     */
    localiseMessage:function(module, screen, message){
      if(!localiseFiles[module]){
        localiseFiles[module] = [];
      }
      if(!localiseFiles[module][screen]){
        localiseFiles[module][screen] = [];
        $.ajax({
          url     :'/PPHP/view/screens/' + module + '/' + screen + '/' + screen + '_' + localise + '.localise',
          type    :'GET',
          dataType:'text',
          async   :false,
          success :function(data){
            var rows = data.split("\n");
            for(var i in rows){
              var element = rows[i].split('=');
              localiseFiles[module][screen][element[0]] = element[1];
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
     */
    setLanguage:function(lang){
      localise = lang;
    }
  }
}();