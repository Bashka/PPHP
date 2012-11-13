var PJS = PJS || {};
PJS.log = function(){
  /*
   * Представление уведомителя.
   * @var object JQuery набор из одного элемента.
   */
  var informant = $('<div style="overflow: auto; color: red; text-align: center; min-width: 20px; min-height: 20px; max-height: 300px; position: fixed; top: 5px; left: 5px; border: 1px solid #000000; border-radius: 5px; background: black">!</div>');
  /*
   * Таймер, отвечающий за скрытие уведомителя.
   * @var object
   */
  var timer;

  /*
   * Метод позволяет подготовить увидомителя к показу иключения.
   */
  var open = function(){
      informant.css('text-align', 'left');
      informant.css('color', 'white');
    },

  /*
     * Метод позволяет вернуть уведомителя в свернутый формат.
     */
    close = function(){
      informant.html('!');
      informant.css('text-align', 'center');
      informant.css('color', '#ff0000');
    };

  $(function(){
    $(document.body).append(informant);
    informant.hide();
  });

  informant.on('click', function(event){
   clearTimeout(timer);
    if(informant.html() == '!'){
      // Открытие последнего исключения
      if(!event.ctrlKey){
        var lastError = PJS.log.getLastException();
        informant.html(toStringException(lastError));
      }
      // Открытие всего журнала
      else{
        informant.html(PJS.log.toStringJournal());
      }
      open();
    }
    // Закрытие уведомления
    else{
      close();
      informant.hide(true);
    }
  });

  /*
   * Метод служит для вызова уведомителя на экран с последующим его уходом в случае бездействия.
   */
  var showInformant = function(){
    informant.stop();
    informant.show(true);
    if(timer){
      clearTimeout(timer);
    }
    timer = setTimeout(function(){
      informant.hide(true);
    }, 5000)
  };

  /*
   * Метод преобразует объект исключения в строку.
   * @param object e Преобразуемое исключение.
   * @return string
   */
  var toStringException = function(e){
    return '-----' + e.time + '-----' + "<br />" +
      ' Exception: <span style="color: red">' + e.type + "</span><br />" +
      ' Message: <b>' + e.message + "</b><br />" +
      ' Code: ' + e.code + "<br />" +
      ' File: ' + e.file + "<br />" +
      ' Line: ' + e.line + "<br /><br />";
  };

  return {
    /*
     * Журнал исключений.
     */
    journal:[],

    /*
     * Метод служит для добавления исключения в журнал.
     * Вызов метода всегда сопровождается показом уведомителя.
     * @param object exc Добавляемое исключение.
     */
    addException:function(exc){
      exc.time = new Date().toLocaleTimeString();
      this.journal.push(exc);
      showInformant();
    },

    /*
     * Метод преобразует весь журнал в строку.
     */
    toStringJournal:function(){
      var journal = '';
      for(var i in this.journal){
        journal += toStringException(this.journal[i]);
      }
      return journal;
    },

    /*
     * Метод возвращает самое позднее исключение в журнале.
     */
    getLastException:function(){
      return this.journal[this.journal.length - 1];
    }
  }
}();