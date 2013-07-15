YUI.add('PJS.screens.Console.browse.ConsoleIn', function(Y){
  function ConsoleIn(cnf){
    ConsoleIn.superclass.constructor.apply(this, arguments);
  }

  ConsoleIn.NAME = 'consoleIn';
  ConsoleIn.ARG_INPUT = '<input type="text" />';
  ConsoleIn.DEFAULT_TIMEOUT = 5000;
  ConsoleIn.ATTRS = {
    sendButton: {
      value: Y.Node.create('<input type="button" title="Send" value=">"/>'),
      readOnly: true
    },
    module: {
      value: Y.Node.create('<input type="text" title="Module name"/>'),
      readOnly: true
    },
    action: {
      value: Y.Node.create('<input type="text" title="Action"/>'),
      readOnly: true
    },
    argBox: {
      value: Y.Node.create('<span></span>'),
      readOnly: true
    },
    args: {
      value: []
    },
    argAddButton: {
      value: Y.Node.create('<input type="button" title="Insert argument" value="+"/>'),
      readOnly: true
    }
  };

  Y.extend(ConsoleIn, Y.Widget, {
    _successAnswer: function(answer){
      this.fire('PJS.screens.Console.browse.ConsoleIn:successAnswer', {answer: answer});
    },

    _failureAnswer: function(error){
      this.fire('PJS.screens.Console.browse.ConsoleIn:failureAnswer', {error: error});
    },

    renderUI: function(){
      Y.use('autocomplete-filters');
      var cb = this.get('contentBox');
      cb.append(this.get('sendButton'));
      cb.append(this.get('module'));
      this.get('module').plug(Y.Plugin.AutoComplete, {
        resultFilters: 'startsWith'
      });
      cb.append('::');
      cb.append(this.get('action'));
      this.get('action').plug(Y.Plugin.AutoComplete, {
        resultFilters: 'startsWith'
      });
      cb.append('(');
      cb.append(this.get('argBox'));
      cb.append(this.get('argAddButton'));
      cb.append(')');
    },

    bindUI: function(){
      var context = this;
      this.get('module').on('focus', function(){
        context.fire('PJS.screens.Console.browse.ConsoleIn:moduleFocus');
      });

      this.get('action').on('focus', function(){
        context.fire('PJS.screens.Console.browse.ConsoleIn:actionFocus');
      });

      this.get('argAddButton').on('click', function(){
        context.addArgument();
        context.fire('PJS.screens.Console.browse.ConsoleIn:addArgument');
      });

      this.get('sendButton').on('click', function(){
        context.fire('PJS.screens.Console.browse.ConsoleIn:clickSend');
      });
    },

    empty: function(){
      this.setModule('');
      this.setAction('');
      this.emptyArguments();
    },

    getModule: function(){
      return this.get('module').getDOMNode().value;
    },

    setModule: function(value){
      this.get('module').getDOMNode().value = value;
    },

    setModuleAutoComplete: function(modules){
      this.get('module').ac.set('source', modules);
    },

    getAction: function(){
      return this.get('action').getDOMNode().value;
    },

    setAction: function(value){
      this.get('action').getDOMNode().value = value;
    },

    setActionAutoComplete: function(actions){
      this.get('action').ac.set('source', actions);
    },

    addArgument: function(value){
      value = value || '';

      var arg = Y.Node.create(ConsoleIn.ARG_INPUT).setAttribute('value', value);
      this.get('argBox').append(arg);
      arg.focus();
      var args = this.get('args');
      args.push(arg);
      this.set('args', args);
    },

    addArguments: function(values){
      for(var i in values){
        this.addArgument(values[i]);
      }
    },

    emptyArguments: function(){
      this.get('argBox').empty();
      this.set('args', []);
    },

    getArgumentsValues: function(){
      var args = this.get('args'),
        result = [];
      for(var i in args){
        result.push(args[i].getDOMNode().value);
      }
      return result;
    },

    loadArgs: function(){
      var module = this.getModule(),
        action = this.getAction();
      if(module != '' && action != ''){
        Y.PJS.services.Query.query('Console', 'getMethodArgs', {
          data: [module, action],
          context: this,
          success: function(answer){
            this.emptyArguments();
            if(answer.length > 0){
              this.addArguments(answer);
            }
          }
        });
      }
    },

    send: function(timeout){
      timeout = timeout || ConsoleIn.DEFAULT_TIMEOUT;
      var module = this.getModule(),
        action = this.getAction(),
        args = this.getArgumentsValues();
      if(module != '' && action != ''){
        Y.PJS.services.Query.query(module, action, {
          data: args,
          timeout: timeout,
          context: this,
          success: this._successAnswer,
          failure: this._failureAnswer
        });
        this.empty();
        this.fire('PJS.screens.Console.browse.ConsoleIn:request', {module: module, action: action, args: args});
      }
    }
  });

  Y.namespace('PJS.screens.Console.browse').ConsoleIn = ConsoleIn;
}, '1.0', {requires: ['widget', 'node', 'autocomplete', 'autocomplete-filters', 'PJS.services.Query']});