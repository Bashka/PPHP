YUI.add('PJS.widgets.MenuTape', function(Y){
  function MenuTape(cnf){
    MenuTape.superclass.constructor.apply(this, arguments);
  }

  MenuTape.NAME = 'menuTape';
  MenuTape.HTML_PARSER = {
    items: function(node){
      var items = node.all('> li'),
        result = [];
      var context = this;
      items.each(function(item){
        result.push(new Y.PJS.widgets.MenuItem({
          srcNode: item,
          parent: context
        }));
      });
      return result;
    }
  };
  MenuTape.ATTRS = {
    isRoot: {
      value: true,
      writeOnce: 'initOnly'
    },
    items: {
      value: []
    },
    parent: {},
    width: {}
  };

  Y.extend(MenuTape, Y.Widget, {
    renderUI: function(){
      var bb = this.get('boundingBox');
      if(this.get('isRoot') == true){
        bb.addClass('yui3-menutape-root');
      }
      else{
        bb.addClass('yui3-menutape-sub').setStyle('position', 'absolute');

        var parentTape = this.get('parent').get('parent').get('boundingBox'),
          label = this.get('parent').get('boundingBox');
        if(parentTape.hasClass('yui3-menutape-sub')){
          bb.setStyle('marginLeft', parseInt(parentTape.get('offsetWidth'))-parseInt(label.getStyle('paddingRight'))-5);
          bb.setStyle('marginTop', -parseInt(label.one('span').getStyle('fontSize')));
          bb.addClass('yui3-menutape-multisub');
        }
      }

      var items = this.get('items');
      for(var i in items){
        items[i].render();
      }

      this.close();
    },

    close: function(){
      if(this.get('isRoot') == false){
        this.get('boundingBox').addClass('yui3-menutape-close');
        this.get('boundingBox').removeClass('yui3-menutape-open');
        this.fire('PJS.widgets.MenuTape:close');
      }
    },

    open: function(){
      if(this.get('isRoot') == false){
        this.get('boundingBox').addClass('yui3-menutape-open');
        this.get('boundingBox').removeClass('yui3-menutape-close');
        this.fire('PJS.widgets.MenuTape:open');
      }
    }
  });

  Y.namespace('PJS.widgets').MenuTape = MenuTape;
}, '1.0', {requires: ['widget', 'event', 'node', 'PJS.widgets.MenuItem']});
