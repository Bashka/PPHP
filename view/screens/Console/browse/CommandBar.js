YUI.add('PJS.screens.Console.browse.CommandBar', function(Y){
  function CommandBar(cnf){
    CommandBar.superclass.constructor.apply(this, arguments);
  }

  CommandBar.NAME = 'commandBar';

  Y.extend(CommandBar, Y.Widget, {
    _addElement: function(elements, type){
      this.empty();
      var cb = this.get('contentBox');
      for(var i in elements){
        cb.append('<div class="PJS.screens.Console.browse.CommandBar.'+type+'">'+elements[i]+'</div>');
      }
    },

    _addModules: function(elements){
      this._addElement(elements, 'module');
      this.fire('PJS.screens.Console.browse.CommandBar:modulesLoad', {modules: elements});
    },

    _addActions: function(elements){
      this._addElement(elements, 'action');
      this.fire('PJS.screens.Console.browse.CommandBar:actionsLoad', {actions: elements});
    },

    renderUI: function(){
      this.empty();
    },

    bindUI: function(){
      var context = this;
      this.get('contentBox').delegate('click', function(e){
        if(e.currentTarget.getAttribute('class') == 'PJS.screens.Console.browse.CommandBar.module'){
          context.fire('PJS.screens.Console.browse.CommandBar:selectModule', {module: e.currentTarget.getHTML()});
        }
        else if(e.currentTarget.getAttribute('class') == 'PJS.screens.Console.browse.CommandBar.action'){
          context.fire('PJS.screens.Console.browse.CommandBar:selectAction', {action: e.currentTarget.getHTML()});
        }
      }, 'div');
    },

    empty: function(){
      this.get('contentBox').empty();
    },

    setModules: function(){
      Y.PJS.services.Query.query('Console', 'getModulesNames', {
        success: this._addModules,
        context: this
      });
    },

    setActions: function(module){
      Y.PJS.services.Query.query('Console', 'getModuleActions', {
        data: [module],
        success: this._addActions,
        context: this
      });
    }
  });

  Y.namespace('PJS.screens.Console.browse').CommandBar = CommandBar;
}, '1.0', {requires: ['widget', 'node', 'PJS.services.Query']});