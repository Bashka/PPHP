YUI().use('node', 'event', function(Y){
  window.PJS = window.PJS || {};
  window.PJS.log = function(){
    /*
     * Представление уведомителя.
     * @var {Node} Node набор из одного элемента.
     */
    var informant = Y.Node.create('<div style="overflow: auto; color: red; text-align: center; min-width: 20px; min-height: 20px; max-height: 300px; position: fixed; top: 5px; left: 5px; border: 1px solid #000000; border-radius: 5px; background: black">!</div>');
    /*
     * Таймер, отвечающий за скрытие уведомителя.
     * @var object
     */
    var timer;

    /*
     * Метод позволяет подготовить увидомителя к показу иключения.
     */
    var open = function(){
        informant.setStyle('color', 'white');
      },

    /*
     * Метод позволяет вернуть уведомителя в свернутый формат.
     */
      close = function(){
        informant.setHTML('!');
        informant.setStyle('color', '#ff0000');
      };

    Y.on('domready', function(){
      Y.one('body').append(informant);
      informant.hide();
    });

    informant.on('click', function(event){
      clearTimeout(timer);
      if(informant.getHTML() == '!'){
        var lastError = PJS.log.getLastException(),
          strError = toStringException(lastError),
          trace = lastError.trace;
        for(var i in trace){
          strError += toStringException(trace[i]);
        }
        informant.setHTML(strError);
        open();
      }
      // Закрытие уведомления
      else{
        close();
        informant.hide();
      }
    });

    /*
     * Метод служит для вызова уведомителя на экран с последующим его уходом в случае бездействия.
     */
    var showInformant = function(){
      informant.show();
      if(timer){
        clearTimeout(timer);
      }
      timer = setTimeout(function(){
        informant.hide();
      }, 5000)
    };

    /*
     * Метод преобразует объект исключения в строку.
     * @param object e Преобразуемое исключение.
     * @return string
     */
    var toStringException = function(e){
      var result = '';
      if(e.time !== undefined){
        result += '-----' + e.time + '-----' + "<br />";
      }
      else{
        result += '-----'+"<br />";
      }
      if(e.type !== undefined){
        result += ' Exception: <span style="color: red">' + e.type + "</span><br />";
      }
      if(e.message !== undefined){
        result += ' Message: ' + e.message + "<br />";
      }
      if(e.class !== undefined){
        result += ' Class exception: ' + e.class + "<br />";
      }
      if(e.code !== undefined){
        result += ' Code: ' + e.code + "<br />";
      }
      result += ' File: ' + e.file + "<br />" + ' Line: ' + e.line + "<br />";
      result += ' Buffer: ' + e.buffer + "<br /><br />";
      return result;
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
});