YUI.add('PJS.screens.Console.browse.Controller', function(Y){
  function Controller(cnf){
    Controller.superclass.constructor.apply(this, arguments);
  }

  Y.extend(Controller, Y.PJS.classes.Controller, {
    initScreen: function(){
      this._initComplete();
    },

    hotKeyInit: function(node){
      var context = this;
      // Обработка передачи команды серверу при нажатии ctrl+Enter
      node.widget.addCommand(true, false, false, 'enter', function(){
        context.get('filling')['consoleIn'].widget.send(context.get('filling')['timeoutQuery'].getDOMNode().value*1000);
        return true;
      });
    },

    consoleOutInit: function(node){
      node.widget.addAnswer('Welcome to the Delphinum Console');
    },

    consoleOutListen: function(node){
      var context = this;
      // Обработка повтора команды из консоли вывода
      node.widget.on('PJS.screens.Console.browse:clickRequest', function(data){
        var ci = context.get('filling')['consoleIn'].widget;
        ci.setModule(data.request.PJS_command.module);
        ci.setAction(data.request.PJS_command.action);
        ci.emptyArguments();
        ci.addArguments(data.request.PJS_command.args);
      });
      // Обработка просмотра ответа
      node.widget.on('PJS.screens.Console.browse:clickAnswer', function(data){
        if(data.answer.PJS_answer != undefined){
          alert(data.answer.PJS_answer);
        }
      });
    },

    consoleInListen: function(node){
      var context = this;
      // Обновление информации о доступных модулях в CommandBar
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:moduleFocus', function(){
        context.get('filling')['commandBar'].widget.setModules();
      });
      // Обновление информации о доступных методах в CommandBar
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:actionFocus', function(){
        var module = this.getModule();
        if(module !== ''){
          context.get('filling')['commandBar'].widget.setActions(module);
        }
      });
      // Добавление аргументов при выборе метода
      node.widget.get('action').ac.on('select', function(data){
        var ci = context.get('filling')['consoleIn'].widget;
        ci.setAction(data.result.text);
        ci.loadArgs();
      });
      // Обработка передачи команды серверу при нажатии кнопки send
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:clickSend', function(){
        node.widget.send(context.get('filling')['timeoutQuery'].getDOMNode().value*1000);
      });
      // Обработка запроса к серверу
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:request', function(data){
        context.get('filling')['consoleOut'].widget.addRequest(data.module, data.action, data.args);
      });
      // Обработка ответа сервера
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:successAnswer', function(data){
        context.get('filling')['consoleOut'].widget.addAnswer(data.answer);
      });
      // Обработка ошибки на сервере
      node.widget.on('PJS.screens.Console.browse.ConsoleIn:failureAnswer', function(data){
        context.get('filling')['consoleOut'].widget.addAnswer(data.error);
      });
    },

    commandBarListen: function(node){
      var context = this;
      // Обновление списка autocomplete модулей
      node.widget.on('PJS.screens.Console.browse.CommandBar:modulesLoad', function(data){
        context.get('filling')['consoleIn'].widget.setModuleAutoComplete(data.modules);
      });
      // Обновление списка autocomplete методов
      node.widget.on('PJS.screens.Console.browse.CommandBar:actionsLoad', function(data){
        context.get('filling')['consoleIn'].widget.setActionAutoComplete(data.actions);
      });
      // Установка модуля при выборе из списка
      node.widget.on('PJS.screens.Console.browse.CommandBar:selectModule', function(data){
        context.get('filling')['consoleIn'].widget.setModule(data.module);
      });
      // Установка метода при выборе из списка
      node.widget.on('PJS.screens.Console.browse.CommandBar:selectAction', function(data){
        var ci = context.get('filling')['consoleIn'].widget;
        ci.setAction(data.action);
        ci.loadArgs();
      });
    },

    uploaderListen: function(node){
      var context = this;

      // Обработка загрукзи файла
      node.widget.on("uploadcomplete", function(event){
        context.get('filling')['consoleOut'].widget.addAnswer(Y.JSON.parse(event.data).answer);
      });
    },

    menuListen: function(node){
      node.widget.on('selectMenu', function(data){
        alert(data.name)
      });

      node.widget.on('selectMenuItem', function(data){
        alert(data.name)
      });
    }
  });

  Y.namespace('PJS.screens.Console.browse').Controller = Controller;
}, '1.0', {requires: ['node', 'base', 'uploader', 'PJS.services.User']});