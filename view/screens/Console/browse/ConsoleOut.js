YUI.add('PJS.screens.Console.browse.ConsoleOut', function(Y){
  function ConsoleOut(cnf){
    ConsoleOut.superclass.constructor.apply(this, arguments);
  }

  ConsoleOut.NAME = 'consoleOut';
  ConsoleOut.REQUEST = Y.Node.create('<div class="pjs-screens-console-browse-request"></div>');
  ConsoleOut.ANSWER = Y.Node.create('<div class="pjs-screens-console-browse-answer"></div>');
  ConsoleOut.ATTRS = {
    numerator: {
      value: 1
    },
    cleaner: {
      value: Y.Node.create('<input type="button" value="clear" style="float: right"/>'),
      readOnly: true
    },
    content: {
      value: Y.Node.create('<div></div>'),
      readOnly: true
    }
  };

  Y.extend(ConsoleOut, Y.Widget, {
    _generateNumerator: function(){
      var num = this.get('numerator');
      this.set('numerator', num+1);
      return num;
    },

    renderUI: function(){
      this.get('contentBox').append(this.get('cleaner'));
      this.get('contentBox').append(this.get('content'))
    },

    bindUI: function(){
      var context = this;

      this.get('contentBox').delegate('click', function(e){
        this.fire('PJS.screens.Console.browse:clickRequest', {request: e.currentTarget});
      }, '.pjs-screens-console-browse-request', this);

      this.get('contentBox').delegate('click', function(e){
        this.fire('PJS.screens.Console.browse:clickAnswer', {answer: e.currentTarget});
      }, '.pjs-screens-console-browse-answer', this);

      this.get('cleaner').on('click', function(){
        context.empty();
        context.set('numerator', 1)
      });
    },

    addRequest: function(module, action, args){
      var request = ConsoleOut.REQUEST.cloneNode().setHTML(this._generateNumerator()+'. > '+module+'::'+action+'('+args+')');
      request.PJS_command = {
        module: module,
        action: action,
        args: args
      };
      var firstChild = this.get('content').one('*');
      if(!firstChild){
        this.get('content').append(request);
      }
      else{
        firstChild.insert(request, 'before');
      }
    },

    addAnswer: function(text){
      var node = ConsoleOut.ANSWER.cloneNode().setHTML(this._generateNumerator()+'. < '+text);
      if((typeof text) == 'object' || (typeof text) == 'array'){
        node.PJS_answer = Y.JSON.stringify(text);
        node.addClass('pjs-screens-console-browse-complexAnswer');
      }
      var firstChild = this.get('content').one('*');
      if(!firstChild){
        this.get('content').append(node);
      }
      else{
        firstChild.insert(node, 'before');
      }

    },

    empty: function(){
      this.get('content').empty();
    }
  });

  Y.namespace('PJS.screens.Console.browse').ConsoleOut = ConsoleOut;
}, '1.0', {requires: ['widget', 'node', 'json-stringify']});