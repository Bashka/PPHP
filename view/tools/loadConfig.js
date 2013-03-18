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
    PJS: {
      base: '../../../tools/PJS/',
      async: false,
      modules: {
        'PJS-core': {
          path: 'PJS.js'
        },
        'PJS-log': {
          path: 'PJSLog.js'
        },
        'PJS-localisation': {
          path: 'PJSLocalisation.js'
        },
        'PJS': {
          use: ['PJS-core', 'PJS-log', 'PJS-localisation']
        }
      }
    }
  }
};