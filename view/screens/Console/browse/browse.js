/**
 * @namespace PPHP\view\screens\Console\browse
 * @author Artur Sh. Mamedbekov
 */
YUI().use('node', 'event', 'tabview', 'uploader', 'json-parse', 'autocomplete', 'autocomplete-filters', 'PJS', function(Y){
  /**
   * Корневой контроллер пользовательского интерфейса.
   * @public
   * @type {Object}
   */
  var rootController = (function(arg){

    /**
     * Представление команды в консоли.
     * @class
     * @constructor
     * @param {String} module
     * @param {String} action
     * @param {String[]|String} [args=undefined]
     */
    function Command(module, action, args){
      this.module = module;
      this.action = action;
      if(args === undefined){
        this.args = [];
      }
      else if(typeof args == 'String'){
        this.args = [args];
      }
      else{
        this.args = args;
      }
    }

    Command.prototype.toString = function(){
      return this.module + '::' + this.action + '(' + this.args.join(', ') + ');';
    };

    /**
     * Объектное представление истории команд.
     * @private
     * @type {Object}
     */
    var history = (function(){
      /**
       * Массив, хранящий историю выполненных команд пользователя.
       * @private
       * @type {Command[]}
       */
      var historyCommands = [],
        /**
         * Указатель текущей команды.
         * @private
         * @type {Integer}
         */
          lkHistory = 0;

      return {
        /**
         * Метод добавляет команду в историю.
         * @public
         * @function
         * @param {Command} command Добавляемая команда.
         */
        push:function(command){
          historyCommands.push(command);
          lkHistory = historyCommands.length;
        },

        /**
         * Метод возвращает предыдущую команду относительно указателя. Если нет команд для возврата, возвращается пустая команда.
         * @public
         * @function
         */
        after:function(){
          var command = historyCommands[--lkHistory];
          if(command == undefined){
            lkHistory = -1;
            return new Command('', '');
          }
          else{
            return command;
          }
        },

        /**
         * Метод возвращает следующую команду относительно указателя. Если нет команд для возврата, возвращается пустая команда.
         * @public
         * @function
         */
        before:function(){
          var command = historyCommands[++lkHistory];
          if(command == undefined){
            lkHistory = historyCommands.length;
            return new Command('', '');
          }
          else{
            return command;
          }
        }
      }
    })();

    /**
     * Объектное представление панели вывода.
     * @private
     * @type {Object}
     */
    var outPanel = (function(){
      /**
       * YUI фассад корневого узла панели вывода.
       * Данный объект генерирует следующие события:
       * - selectCommand - в случае выбора пользователем команды на панели вывода.
       * @private
       * @type {Node}
       */
      var rootNode = arg.DOM.received.outPanel,
        /**
         * Порядковый номер строки.
         * @private
         * @type {Integer}
         */
          nRow = 1;

      /**
       * Метод добавляет сообщение в поле вывода.
       * @private
       * @funtion
       * @param {String} message Текст сообщения.
       * @param {String} color Цвет сообщения.
       * @return {Node} Добавленый узел сообщения.
       */
      var add = function(message, color){
        var messageNode = Y.Node.create('<div class="Console_browse_message" style="color: ' + color + '">' + nRow + '. ' + message + '</div>');
        rootNode.append(messageNode);
        nRow++;
        return messageNode;
      };

      return {
        /**
         * Перечисление доступных цветов для отображения на панели вывода.
         * @public
         * @type {Object}
         */
        colors:{
          COMMAND:'white',
          ANSWER:'silver',
          ERROR:'red'
        },

        /**
         * Метод добавляет команду на панель.
         * @public
         * @function
         * @param {Command} command Добавляемая команда.
         * @param {String} [color=COMMAND] Цвет сообщения.
         */
        addCommand:function(command, color){
          if(color === undefined){
            color = this.colors.COMMAND;
          }
          var messageNode = add('&gt; ' + command, color);
          messageNode.addClass('Console_browse_command');
          messageNode.command = command
          history.push(command);
        },

        /**
         * Метод добавляет сообщение на панель.
         * @public
         * @function
         * @param {String} message Добавляемое сообщение.
         * @param {String} [color=ANSWER] Цвет сообщения.
         */
        addMessage:function(message, color){
          if(color === undefined){
            color = this.colors.ANSWER;
          }
          add('&lt; ' + message, color);
        },

        /**
         * Метод отчищает панель.
         * @public
         * @function
         */
        clear:function(){
          rootNode.empty();
        }
      }
    })();

    /**
     * Объектное представление панели ввода.
     * @private
     * @type {Object}
     */
    var inPanel = (function(){
      /**
       * YUI фассад корневого узла панели ввода.
       * @private
       * @type {Node}
       */
      var rootNode = arg.DOM.received.inPanel,
        /**
         * YUI фассад поля ввода целевого модуля.
         * @private
         * @type {Node}
         */
          moduleInputNode = arg.DOM.received.moduleInput,
        /**
         * YUI фассад поля ввода целевого метода контроллера модуля.
         * @private
         * @type {Node}
         */
          actionInputNode = arg.DOM.received.actionInput,
        /**
         * YUI фассад контейнера параметров, передаваемых методу контроллера модуля.
         * @private
         * @type {Node}
         */
          argsContainer = arg.DOM.received.argsContainer;

      /**
       * Метод возвращает массив значений параметров, заданный в поле ввода.
       * @private
       * @function
       * @return {String[]} Массив значений параметров.
       */
      var getArgs = function(){
        var resultArray = [];
        argsContainer.all('.Console_browse_argInput').each(function(){
          resultArray.push(this.getDOMNode().value);
        });
        return resultArray;
      };

      return {
        /**
         * Метод устанавливает текущее значение для поля ввода целевого модуля.
         * @public
         * @function
         * @param {String} data Устанавливаемые данные.
         */
        setModule:function(data){
          moduleInputNode.getDOMNode().value = data;
        },

        /**
         * Метод устанавливает текущее значение для поля ввода метода контроллера модуля.
         * @public
         * @function
         * @param {String} data Устанавливаемые данные.
         */
        setAction:function(data){
          actionInputNode.getDOMNode().value = data;
        },

        /**
         * Метод устанавливает значения аргументов, передаваемых методу контроллера модуля.
         * @public
         * @function
         * @param {String[]|String} [args] Устанавливаемые данные в виде массива строк или одной строки (в случае определения одного аргумента). Если в метод не передается данный параметр, то массив аргументов обнуляется.
         */
        setArgs:function(args){
          argsContainer.all('.Console_browse_argInput').remove();
          if(typeof args == 'String'){
            args = [args];
          }
          if(typeof args == 'object'){
            for(var i in args){
              this.addArg(args[i]);
            }
            moduleInputNode.focus();
          }
        },

        /**
         * Метод добавляет один параметр с указанными данными на панель ввода.
         * @public
         * @function
         * @param {String} [data] Данные, устанавливаемые в качестве значения добавляемого аргумента. Если этот параметр не задан, аргумент получет в качестве значения пустую строку.
         * @return {Node} Ссылка на YIU фассад добавленного узла input.
         */
        addArg:function(data){
          if(data === undefined){
            data = '';
          }
          var argNode = Y.Node.create('<input class="Console_browse_argInput" type="text" value="' + data + '"/>');
          arg.DOM.received.addArgButton.insertBefore(argNode, arg.DOM.received.addArgButton);
          return argNode;
        },

        /**
         * Метод возвращает указанный узел ввода значения аргумента.
         * @public
         * @function
         * @param {Integer} i Порядковый индекс узла.
         * @return {Node|null} Запрашиваемый узел или null - если указанного поля не существует.
         */
        getArgNode:function(i){
          return arg.DOM.received.argsContainer.all('.Console_browse_argInput').item(i);
        },

        /**
         * Метод определяет текущее состояние поля ввода по данным переданной команды.
         * @public
         * @function
         * @param {Command} command Команда-основание.
         */
        setCommand:function(command){
          this.setModule(command.module);
          this.setAction(command.action);
          this.setArgs(command.args);
        },

        /**
         * Метод отчищает панель ввода.
         * @public
         * @function
         */
        clear:function(){
          this.setModule('');
          this.setAction('');
          this.setArgs();
        },

        /**
         * Метод возвращает текущую команду, введеную пользователем.
         * @public
         * @function
         * @return {Command} Текущая команда на панели ввода.
         */
        getCommand:function(){
          return new Command(moduleInputNode.getDOMNode().value, actionInputNode.getDOMNode().value, getArgs());
        },

        /**
         * Метод выполняет запрос пользователя к серверу.
         * @public
         * @function
         * @return {Boolean} true - в случае успешной передачи запроса и false - в случае неудачи.
         */
        send:function(){
          var command = this.getCommand();
          if(command.module == '' || command.action == '' || typeof command.args != 'object'){
            return false;
          }
          outPanel.addCommand(command);
          this.clear();
          moduleInputNode.focus();

          PJS.core.query(command.module, command.action,
            {
              data:command.args,
              timeout:10000,
              callback:function(answer){
                if(typeof answer == 'object'){
                  var objInfo = '{';
                  for(var i in answer){
                    objInfo += i + ': ' + answer[i] + '; ';
                  }
                  objInfo += '}';
                  answer = objInfo;
                }
                outPanel.addMessage(answer);
              },
              error:function(exc){
                outPanel.addMessage(exc.message, outPanel.colors.ERROR);
              },
              context:this
            });
          return true;
        }
      }
    })();

    /**
     * Объектное представление панели команд.
     * @private
     * @type {Object}
     */
    var commandsList = (function(){
      /**
       * Корневой узел панели.
       * @private
       * @type {Node}
       */
      var rootNode = arg.DOM.received.commandsContainer;

      return {
        /**
         * Метод добавляет переданный массив команд на панель.
         * @public
         * @function
         * @param {String[]} data Добавляемый массив команд.
         */
        setData: function(data){
          rootNode.empty();
          for(var i in data){
            rootNode.append('<div class="Console_browse_command Console_browse_modules">'+data[i]+'</div>');
          }
        }
      }
    })();

    return {
      /**
       * @function
       * @public
       */
      init:function(){

      },

      inPanelListener:function(node){
        node.delegate('keypress', function(e){
          if(e.ctrlKey){
            // Обработка передачи команды серверу при нажатии Enter
            if(e.keyCode == 13 || e.keyCode == 10){
              inPanel.send();
            }

            // Обработка просмотра истории клавишами со стрелками
            if(e.keyCode == 38){
              inPanel.setCommand(history.after());
            }
            else if(e.keyCode == 40){
              inPanel.setCommand(history.before());
            }
          }
        }, 'input[type=text]');
      },

      addArgListener:function(node){
        // Обработка события добавления аргумента на панель ввода
        node.on('click', function(){
          inPanel.addArg().focus();
        });
      },

      moduleInputLoader:function(node){
        // Добавление механизма автозавершения для поля ввода модуля
        node.plug(Y.Plugin.AutoComplete, {
          resultFilters:'startsWith'
        });
      },

      actionInputLoader:function(node){
        // Добавление механизма автозавершения для поля ввода метода модуля
        node.plug(Y.Plugin.AutoComplete, {
          resultFilters:'startsWith'
        });
      },

      moduleInputListener:function(node){
        // Добавление механизма запроса доступных модулей
        node.on('focus', function(){
          PJS.core.query('Console', 'getModulesNames',
            {
              callback:function(answer){
                commandsList.setData(answer);
                arg.DOM.received.moduleInput.ac.set('source', answer);
              }
            });
        });
      },

      actionInputListener:function(node){
        // Добавление механизма запроса доступных методов модулей
        node.on('focus', function(){
          var module = arg.DOM.received.moduleInput.getDOMNode().value;
          if(module != ''){
            PJS.core.query('Console', 'getModuleActions',
              {
                data:[module],
                callback:function(answer){
                  commandsList.setData(answer);
                  arg.DOM.received.actionInput.ac.set('source', answer);
                }
              });
          }
        });

        // Обработка выбора метода модуля с целью добавления используемых им аргументов
        node.ac.on('select', function(data){
          var module = arg.DOM.received.moduleInput.getDOMNode().value,
            action = data.result.text;

          if(module != '' && action != ''){
            inPanel.setArgs();
            PJS.core.query('Console', 'getMethodArgs', {
                data: [module, action],
                callback: function(answer){
                  if(answer.length > 0){
                    inPanel.setArgs(answer);
                    inPanel.getArgNode(0).focus();
                  }
                }
              });
          }
        });
      },

      outPanelListener:function(node){
        // Добавление механизма копирования и исполнения команд по щелчку на панели вывода
        node.delegate('click', function(e){
          inPanel.setCommand(this.command);
          if(e.ctrlKey){
            inPanel.send();
          }
        }, '.Console_browse_command');
      },

      menuLoader:function(node){
        var tabview = new Y.TabView({srcNode:node});
        tabview.render();
      },

      uploaderLoader:function(node){
        var slcB = Y.Node.one('#selectFiles'),
          uplC = Y.Node.one('#uploadFilesContainer'),
          upload = false;

        var uploader = new Y.UploaderHTML5({width:"300px",
          height:"30px",
          multipleFiles:false});
        uploader.render('#selectFiles');

        uplC.hide();
        Y.Node.one('#uploadFilesButton').on('click', function(){
          if(uploader.get("fileList").length > 0 && !upload){
            uploader.upload(uploader.get("fileList")[0], "/PPHP/model/modules/CentralController.php", {module:"Console", active:"uploadFile"});
            upload = true;
          }
        });
        uploader.on("uploadcomplete", function(event){
          uploader.set("fileList", []);
          outPanel.addMessage(Y.JSON.parse(event.data).answer);
          uplC.hide();
          slcB.show();
          upload = false;
        });
        uploader.after("fileselect", function(){
          slcB.hide();
          uplC.show();
        });
      }
    }
  });

  Y.on('domready', function(){
    var arg = {
      DOM:{
        root:Y.Node.one('body'),
        received:{}
      },
      environment:{
        params:''
      }
    };
    Y.Node.all('[dGet]').each(function(){
      arg.DOM.received[this.getAttribute('dGet')] = this;
    });
    rootController = rootController(arg);
    rootController.init();
    Y.Node.all('[dLoader]').each(function(){
      rootController[this.getAttribute('dLoader')](this);
    });
    Y.Node.all('[dListener]').each(function(){
      rootController[this.getAttribute('dListener')](this);
    });
  })
});