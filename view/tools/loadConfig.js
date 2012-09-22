var YUI_config = {
  groups: {
    JQuery: {
      base: '../../tools/JQuery/',
      async: false,
      modules: {
        'JQuery-base': {
          path: 'jquery.js'
        },
        'JQuery-form': {
          path: 'jquery.form.js',
          requests: ['JQuery-base']
        },
        'JQuery-jframe': {
          path: 'jquery.jframe.js',
          requests: ['JQuery-base', 'JQuery-form']
        },
        'JQuery-core': {
          use: ['JQuery-base', 'JQuery-form', 'JQuery-jframe']
        }
      }
    },
    PJS: {
      base: '../../tools/PJS/',
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