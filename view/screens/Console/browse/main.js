YUI().use('PJS.services.Transformer', 'PJS.services.User', 'node', function(Y){
  // Загрузка информации о пользователе
  Y.PJS.services.User.once('PJS.services.User:initializer', function(){
    // Предварительный рендеринг виджетов
    var dwidgets = Y.one('body').all('[dWidget]'),
      widgets = [];

    dwidgets.each(function(node){
      if(node.getAttribute('dWidget') != 'PJS-widgets-Screen'){
        widgets.push(node);
      }
    });
    widgets = new Y.NodeList(widgets);

    // Рендеринг экрана
    Y.PJS.services.Transformer.transform(widgets, function(){
      Y.PJS.services.Transformer.transform(Y.one('#PJS-widgets-Screen-rootScreen'));
    });
  });
});