YUI.add('PJS.widgets.TreeView', function(Y){
  function TreeView(cnf){
    TreeView.superclass.constructor.apply(this, arguments);
  }

  TreeView.NAME = 'treeView';
  TreeView.ATTRS = {
    expand: {
      value: Y.Node.create('<div class="yui3-treeview-expand"></div>')
    },
    label: {
      value: Y.Node.create('<div class="yui3-treeview-label"></div>')
    },
    container: {
      value: Y.Node.create('<div class="yui3-treeview-container"></div>')
    },
    children: {
      value: []
    }
  };
  TreeView.HTML_PARSER = {
    label: function(node){
      return node.one('> .yui3-treeview-label');
    },
    expand: function(node){
      return this.get('label').one('> .yui3-treeview-expand');
    },
    container: function(node){
      return node.one('> .yui3-treeview-content').one('> .yui3-treeview-container');
    },
    children: function(node){
      var childNodes = this.get('container').all('> .yui3-treeview'),
        children = [];
      childNodes.each(function(node){
        children.push(new TreeView({
          srcNode: node
        }).render());
      });
      return children;
    }
  };

  Y.extend(TreeView, Y.Widget, {
    renderUI: function(){
      //alert('render ui');
    },

    bindUI: function(){
      //alert('bind ui');
    },

    syncUI: function(){
      //alert('sync ui');
    }
  });

  Y.namespace('PJS.widgets').TreeView = TreeView;
}, '1.0', {requires: ['widget', 'node', 'event']});