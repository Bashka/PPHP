YUI().use('JQuery-core', 'PJS', 'tabview', 'uploader', 'json-parse', 'autocomplete', 'autocomplete-filters', function(Y){
  PJS.controllers.Console = PJS.controllers.Console || {};

  PJS.controllers.Console.browse = function(){
    /*
     * @field Поле вывода консоли.
     * @private
     * @type JQueryNode
     */
    var screen,
    /*
     * @field Область ввода консоли.
     * @private
     * @type JQueryNode
     */
      console,
      tabview,
      commandsPanel;

    /*
     * @function Метод записывает строку сообщения в вывод консоли.
     * @private
     * @param {string} message Записываемое сообщение.
     * @param {string} color Цвет сообщения.
     */
    var setCommandInConsole = function(message, color){
        color = color || 'white';
        screen.countCommand++;
        screen.append('<div style="color: ' + color + '">' + screen.countCommand + '. ' + message + ';</div>');
        if(screen.countCommand > 20){
          var superfluous = $(screen.children().get(0));
          $('#consoleBox').prepend(superfluous);
          superfluous.hide('normal', function(e){
            $(this).remove();
          });
        }
      },
    /*
     * @function Метод передает команду серверу.
     * @private
     * @param {Command} command Выполняемая команда.
     */
      send = function(command){
        console.args.clearArgs();
        console.module.get(0).value = '';
        console.action.get(0).value = '';
        console.module.focus();

        setCommandInConsole('> ' + command);
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

            setCommandInConsole('< ' + answer, 'silver');
          },
          function(error){
            setCommandInConsole('< ' + error.message, 'red');
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

    var initVars = function(){
        screen = $('#consoleScreen');
        console = $('#console');
        commandsPanel = $('#commands');

        console.module = $('#module');
        console.action = $('#action');
        console.args = $('#argsCommand');
        console.args.plusArg = $('#argsCommand input[type=button]');

        console.args.addInput = function(value){
          var button = this.children('input[type=button]');
          if(this.children().length > 1){
            button.before('<span>,</span>')
          }
          value = value || '';
          var newArg = $('<input type="text" value="'+value+'"/>');
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
            commandsPanel.append('<div>'+this+'</div>');
          });
        }

        commandsPanel.clear = function(){
          this.children().remove();
        }
      },

      initListeners = function(){
        console.module.on('keyup', function(e){
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

        console.action.on('keyup', function(e){
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

        console.args.on('keyup', function(e){
          if(e.target.value.length > 0){
            $(e.target).css('width', (e.target.value.length + 1) * 9 + 'px');
          }
          else{
            $(e.target).css('width', '9px');
          }
        });

        console.args.plusArg.on('click', function(e){
          if(!e.ctrlKey){
            console.args.addInput();
          }else{
            var n = parseInt(prompt('Введите количество аргументов.'));
            if(!isNaN(n) && n > 0){
              for(var i=0; i<n;i++){
                console.args.addInput();
                console.args.children('input[type=text]').get(0).focus();
              }
            }
          }
        });

        console.module.on('focus', function(e){
            PJS.query('Console', 'getModulesNames', function(answer){
              console.moduleYUI.ac.set('source', answer);

              commandsPanel.clear();
              commandsPanel.add(answer);
            });
        });

        console.action.on('focus', function(e){
          commandsPanel.clear();

          var value = console.module.get(0).value;
          if(value != ''){
            PJS.query('Console', 'getModuleActions', {
              module: value
            }, function(answer){
              console.actionYUI.ac.set('source', answer);

              commandsPanel.add(answer);
            });
          }
        });

        console.actionYUI.ac.on('select', function(data){
          var module = console.module.get(0).value,
            action = data.result.text;

          if(module != '' && action != ''){
            console.args.clearArgs();
            PJS.query('Console', 'getMethodArgs', {
              module: module,
              action: action
            }, function(answer){
              if(answer.length > 0){
                $(answer).each(function(){
                  console.args.addInput(this);
                });
                console.args.children('input[type=text]').get(0).focus();
              }
            });
          }
        });

        console.on('keypress', function(e){
          if(e.keyCode == 13){
            if(console.module.get(0).value != '' && console.action.get(0).value != ''){
              var command = new Command(console.module.get(0).value, console.action.get(0).value);
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
          setCommandInConsole('< '+Y.JSON.parse(event.data).answer, 'silver');
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

