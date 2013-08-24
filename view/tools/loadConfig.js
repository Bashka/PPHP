var YUI_config = {
  groups: {
    JQuery: {
      base: '../../../tools/JQuery/',
      async: false,
      modules: {
        'JQuery': {
          path: 'jquery.js'
        }
      }
    },
    PJS_widgets: {
      base: '/PPHP/view/tools/PJS/widgets/',
      async: false,
      modules: {
        'PJS.widgets.LoadingIndicator': {
          path: 'loadingIndicator/LoadingIndicator.js',
          requires: ['widget', 'node', 'PJS.services.Query', 'event']
        },
        'PJS.widgets.Screen': {
          path: 'Screen.js',
          requires: ['widget', 'node', 'node-load', 'get', 'PJS.classes.Controller', 'PJS.services.Transformer', 'event', 'PJS.services.User']
        },
        'PJS.widgets.UploaderHTML': {
          path: 'UploaderHTML.js',
          requires: ['widget', 'node', 'uploader', 'event']
        },
        'PJS.widgets.HotKey': {
          path: 'HotKey.js',
          requires: ['widget', 'node', 'event']
        },
        'PJS.widgets.MenuTape': {
          path: 'Menu/MenuTape.js',
          requires: ['widget', 'event', 'node', 'PJS.widgets.MenuItem']
        },
        'PJS.widgets.MenuItem': {
          path: 'Menu/MenuItem.js',
          requires: ['widget', 'event', 'node', 'PJS.widgets.MenuTape']
        },
        'gallery-contextmenu-view': {
          path: 'gallery-contextmenu-view/gallery-contextmenu-view-min.js',
          skinnable: true,
          requires: ['base-build', 'view', 'overlay', 'event-mouseenter', 'template']
        },
        'PJS.widgets.TreeView': {
          path: 'TreeView/TreeView.js',
          skinnable: true,
          requires: ['widget', 'node', 'event']
        }
      }
    },
    PJS_services: {
      base: '/PPHP/view/tools/PJS/services/',
      async: false,
      modules: {
        'PJS.services.Query': {
          path: 'Query.js',
          requires: ['io-base', 'json-parse', 'json-stringify', 'event', 'node', 'oop']
        },
        'PJS.services.User': {
          path: 'User.js',
          requires: ['PJS.services.Query', 'event', 'oop']
        },
        'PJS.services.Transformer': {
          path: 'Transformer.js',
          requires: ['node']
        }
      }
    },
    PJS_classes: {
      base: '/PPHP/view/tools/PJS/classes/',
      async: false,
      modules: {
        'PJS.classes.Controller': {
          path: 'Controller.js',
          requires: ['base', 'PJS.services.Query']
        }
      }
    },
    PJS_controllers: {
      base: '/PPHP/view/screens/',
      async: false,
      modules: {}
    }
  }
};