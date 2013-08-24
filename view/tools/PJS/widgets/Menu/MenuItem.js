YUI.add('PJS.widgets.MenuItem', function(Y){
  function MenuItem(cnf){
    MenuItem.superclass.constructor.apply(this, arguments);
  }

  MenuItem.NAME = 'menuItem';
  MenuItem.HTML_PARSER = {
    title: function(node){
      return node.one('> span').getHTML();
    },
    submenu: function(node){
      var children = node.one('> ul');

      if(children !== null){
        return new Y.PJS.widgets.MenuTape({
          srcNode: children,
          isRoot: false,
          parent: this
        });
      }
      else{
        return false;
      }
    }
  };
  MenuItem.ATTRS = {
    title: {},
    submenu: {},
    parent: {}
  };

  Y.extend(MenuItem, Y.Widget, {
    renderUI: function(){
      var submenu = this.get('submenu');
      if(submenu !== false){
        submenu.render();
      }
    },

    bindUI: function(){
      var context = this,
        cb = this.get('contentBox');

      cb.delegate('click', function(){
        context.fire('PJS.widgets.MenuItem:select', {itemText: this.getHTML()});
      }, '> span');

      if(this.get('submenu') != false){
        cb.on('mouseover', function(){
          context.get('submenu').open();
        });

        cb.on('mouseout', function(){
          context.get('submenu').close();
        });
      }
    }
  });

  Y.namespace('PJS.widgets').MenuItem = MenuItem;
}, '1.0', {requires: ['widget', 'event', 'node', 'PJS.widgets.MenuTape']});
