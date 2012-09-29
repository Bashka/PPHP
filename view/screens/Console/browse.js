YUI().use('JQuery-core', 'PJS', 'tabview', 'uploader', 'json-parse', 'autocomplete', 'autocomplete-filters', function(Y){
  PJS.controllers.Console = PJS.controllers.Console || {};

  PJS.controllers.Console.browse = function(){
    /*
     * @function Метод добавляет аргумент в команду.
     * @public
     * @param {string} arg Добавляемая команда.
     */
    Command.prototype.addArg = function(arg){
      this.args.push(arg.trim());
    }

    Command.prototype.toString = function(){
      return this.module + '::' + this.action + '(' + this.args + ')';
    }

    /*
     * @field Объект, отвечающий за работу области вывода.
     * @private
     * @type Object
     */
    var screen = (function(){
        // Private properties
        /*
         * @field Общее число сообщений.
         * @private
         * @type integer
         */
        var countCommands = 0,
        /*
           * @field Ссылка на узел области вывода консоли.
           * @private
           * @type jQueryNode
           */
          node = $('#consoleScreen');

        // Private methods
        /*
         * @function Метод добавляет сообщений в область вывода.
         * @private
         * @param {DOMNode} message Добавляемый узел с сообщением.
         */
        var addMessage = function(message){
          countCommands++;
          node.append(message);

          if(countCommands > 20){
            var superfluous = $(node.children().get(0));
            $('#consoleBox').prepend(superfluous);
            superfluous.hide('normal', function(e){
              $(this).remove();
            });
          }
        };

        // Init
        node.delegate('.Console_commandScreen', 'click', function(e){
          var command = this.command;
          console.module.value = command.module;
          console.action.value = command.action;

          var args = command.args;
          for(var i in args){
            console.args.addInput(args[i]);
          }
        });

        return {
          /*
           * @function Метод добавляет введеную команду в область вывода консоли.
           * @private
           * @param {Command} command Добавляемая команда.
           */
          addCommand: function(command){
            var messageNode = $('<div class="Console_messageScreen Console_commandScreen" style="color: white; cursor: pointer">' + countCommands+ '. > ' + command + ';</div>');
            messageNode.get(0).command = command;
            addMessage(messageNode);
          },
          /*
           * @function Метод добавляет полученный ответ от сервера в область вывода консоли.
           * @private
           * @param {string} answer Добавляемое сообщение.
           */
          addAnswer: function(answer){
            addMessage($('<div class="Console_messageScreen" style="color: silver">' + countCommands+ '. < ' + answer + ';</div>'));
          },
          /*
           * @function Метод добавляет полученную от сервера ошибку в область вывода консоли.
           * @private
           * @param {string} error Добавляемое сообщение.
           */
          addError: function(error){
            addMessage($('<div class="Console_messageScreen" style="color: red">' + countCommands+ '. < ' + error + ';</div>'));
          }
        }
      })(),
    /*
     * @field Область ввода консоли.
     * @private
     * @type JQueryNode
     */
      console,
      tabview,
      commandsPanel;

    /*
     * @function Метод передает команду серверу.
     * @private
     * @param {Command} command Выполняемая команда.
     */
      var send = function(command){
        console.args.clearArgs();
        console.module.value = '';
        console.action.value = '';
        console.module.focus();

        screen.addCommand(command);
        PJS.query(command.module, command.action, command.args,
          function(answer){
            if(typeof answer == 'object'){
              var objInfo = '{';
              for(var i in answer){
                objInfo += i + ': ' + answer[i] + '; ';
              }
              objInfo += '}';
              answer = objInfo;
            }

            screen.addAnswer(answer);
          },
          function(error){
            screen.addError(error.message);
          });
      };

    /*
     * @class {Command} Класс является представлением команды консоли.
     * @public
     * @param {string} module Целевой модуль.
     * @param {string} action Вызываемый метод модуля.
     */
    function Command(module, action){
      this.module = module.trim();
      this.action = action.trim();
      this.args = [];
    }

    var initVars = function(){
        console = $('#console');
        commandsPanel = $('#commands');

        console.module = $('#module').get(0);
        console.action = $('#action').get(0);
        console.args = $('#argsCommand');
        console.args.plusArg = $('#argsCommand input[type=button]');

        console.args.addInput = function(value){
          var button = this.children('input[type=button]');
          if(this.children().length > 1){
            button.before('<span>,</span>')
          }
          value = value || '';
          var newArg = $('<input type="text" value="' + value + '"/>');
          button.before(newArg);
          newArg.focus();
          newArg.keyup();
        }

        console.args.clearArgs = function(){
          if(console.args.children().length > 1){
            $('#argsCommand input[type=text]').add('#argsCommand span').remove();
          }
        }

        console.moduleYUI = Y.one('#module');
        console.moduleYUI.plug(Y.Plugin.AutoComplete, {
          resultFilters:'startsWith'
        });

        console.actionYUI = Y.one('#action');
        console.actionYUI.plug(Y.Plugin.AutoComplete, {
          resultFilters:'startsWith'
        });

        commandsPanel.add = function(modulesName){
          $(modulesName).each(function(){
            commandsPanel.append('<div>' + this + '</div>');
          });
        }

        commandsPanel.clear = function(){
          this.children().remove();
        }
      },

      initListeners = function(){
        // Набор модуля
        $(console.module).on('keyup', function(e){
          if(this.value.length > 8){
            $(this).css('width', (this.value.length + 1) * 9 + 'px');
          }
          else{
            $(this).css('width', '80px');
          }

          var firstCharCode = this.value.charCodeAt(0);
          if(!(firstCharCode > 64 && firstCharCode < 91)){
            this.value = this.value.substr(0, 1).toUpperCase() + this.value.substr(1);
          }
        });
        // Набор метода
        $(console.action).on('keyup', function(e){
          if(this.value.length > 13){
            $(this).css('width', (this.value.length + 1) * 9 + 'px');
          }
          else{
            $(this).css('width', '131px');
          }

          var firstCharCode = this.value.charCodeAt(0);
          if(firstCharCode > 64 && firstCharCode < 91){
            this.value = this.value.substr(0, 1).toLowerCase() + this.value.substr(1);
          }
        });
        // Набор аргумента
        console.args.on('keyup', function(e){
          if(e.target.value.length > 0){
            $(e.target).css('width', (e.target.value.length + 1) * 9 + 'px');
          }
          else{
            $(e.target).css('width', '9px');
          }
        });
        // Ручное добавление аргумента
        console.args.plusArg.on('click', function(e){
          if(!e.ctrlKey){
            console.args.addInput();
          }
          else{
            var n = parseInt(prompt('Введите количество аргументов.'));
            if(!isNaN(n) && n > 0){
              for(var i = 0; i < n; i++){
                console.args.addInput();
                console.args.children('input[type=text]').get(0).focus();
              }
            }
          }
        });
        // Фокус модуля
        $(console.module).on('focus', function(e){
          PJS.query('Console', 'getModulesNames', function(answer){
            console.moduleYUI.ac.set('source', answer);

            commandsPanel.clear();
            commandsPanel.add(answer);
          });
        });
        // Фокус метода
        $(console.action).on('focus', function(e){
          commandsPanel.clear();

          var value = console.module.value;
          if(value != ''){
            PJS.query('Console', 'getModuleActions', {
              module:value
            }, function(answer){
              console.actionYUI.ac.set('source', answer);

              commandsPanel.add(answer);
            });
          }
        });
        // Выбор метода из выпадающего списка и отображение аргументов
        console.actionYUI.ac.on('select', function(data){
          var module = console.module.value,
            action = data.result.text;

          if(module != '' && action != ''){
            console.args.clearArgs();
            PJS.query('Console', 'getMethodArgs', {
              module:module,
              action:action
            }, function(answer){
              if(answer.length > 0){
                $(answer).each(function(){
                  console.args.addInput(this);
                });
                var firstArg = console.args.children('input[type=text]').get(0);
                firstArg.focus();
                firstArg.select();
              }
            });
          }
        });
        // Отправка команды
        console.on('keypress', function(e){
          if((e.keyCode == 13 || e.keyCode == 10) && e.ctrlKey){
            if(console.module.value != '' && console.action.value != ''){
              var command = new Command(console.module.value, console.action.value);
              var args = $('#argsCommand input[type=text]');
              args.each(function(k, v){
                command.addArg(v.value);
              });
              send(command);
            }
          }
        });
      };

    return {
      init:function(){
        initVars();
        initListeners();

        screen.countCommand = 0;

        tabview = new Y.TabView({srcNode:'#consoleMenu'});
        tabview.render();

        var uploader = new Y.UploaderHTML5({width:"300px",
          height:"30px",
          multipleFiles:false});
        uploader.render("#selectFilesButtonContainer");
        var upload = false;
        var uploadButton = $('#uploadFilesButton'),
          selectButton = $('#selectFilesButtonContainer');
        uploadButton.hide();
        uploadButton.on('click', function(event){
          if(uploader.get("fileList").length > 0 && !upload){
            uploader.upload(uploader.get("fileList")[0], "/PPHP/model/modules/CentralController.php", {module:"Console", active:"uploadFile"});
            upload = true;
          }
        });
        uploader.on("fileselect", function(event){
          selectButton.hide();
        });
        uploader.on("uploadcomplete", function(event){
          uploader.set("fileList", []);
          screen.addAnswer(Y.JSON.parse(event.data).answer);
          uploadButton.hide();
          selectButton.show();
          upload = false;
        });
        uploader.after("fileselect", function(event){
          uploadButton.show();
        });
      }
    }
  }();

  $(function(){
    PJS.controllers.Console.browse.init();
  });
});

