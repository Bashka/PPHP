/**
 * @namespace PPHP\view\tools\PJS\widgets\HotKey
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.widgets.HotKey', function(Y){
  /**
   * Данный виджет представляет обработчик нажатия клавиш.
   * После рендеринга виджет не виден.
   * @param {Object} cnf Параметры класса.
   * @constructor
   * @this {Screen}
   */
  function HotKey(cnf){
    HotKey.superclass.constructor.apply(this, arguments);
  }

  HotKey.NAME = 'hotKey';
  /**
   * Коды клавиш.
   * Каждому наименованию клавиши соответствует ее код или массив кодов, определенных при изменении раскладки клавиатуры.
   * @public
   * @type {Object}
   */
  HotKey.KEYS_CODE = {
       /* En  en  ru    Ru */
    'a': [65, 97, 1092, 1060],
    'b': [66, 98, 1080, 1048],
    'c': [67, 99, 1089, 1057],
    'd': [68, 100, 1074, 1042],
    'e': [69, 101, 1077, 1045],
    'f': [70, 102, 1072, 1040],
    'g': [71, 103, 1087, 1055],
    'h': [72, 104, 1088, 1056],
    'i': [73, 105, 1096, 1064],
    'j': [74, 106, 1086, 1054],
    'k': [75, 107, 1083, 1051],
    'l': [76, 108, 1076, 1044],
    'm': [77, 109, 1100, 1068],
    'n': [78, 110, 1090, 1058],
    'o': [79, 111, 1097, 1065],
    'p': [80, 112, 1079, 1047],
    'q': [81, 113, 1081, 1049],
    'r': [82, 114, 1082, 1050],
    's': [83, 115, 1099, 1067],
    't': [84, 116, 1077, 1045],
    'u': [85, 117, 1075, 1043],
    'v': [86, 118, 1084, 1052],
    'w': [87, 119, 1094, 1062],
    'x': [88, 120, 1095, 1063],
    'y': [89, 121, 1085, 1053],
    'z': [90, 122, 1103, 1071],
    '1': 49,
    '2': 50,
    '3': 51,
    '4': 52,
    '5': 53,
    '6': 54,
    '7': 55,
    '8': 56,
    '9': 57,
    '0': 48,
       /* -   _ */
    '-': [45, 95],
       /* +   = */
    '+': [61, 43],
    '*': 42,
    '/': 47,
    'enter': 13,
    'up': 38,
    'down': 40,
    'left': 37,
    'right': 39,
    'space': 32,
    'tab': 9,
    'backspace': 8,
    'esc': 27,
    'home': 36,
    'end': 35,
    'pgup': 33,
    'pgdn': 34,
    'del': 46,
    'pause': 19,
    'f1': 112,
    'f2': 113,
    'f3': 114,
    'f4': 115,
    'f5': 116,
    'f6': 117,
    'f7': 118,
    'f8': 119,
    'f9': 120,
    'f10': 121,
    'f11': 122,
    'f12': 123
  };
  HotKey.ATTRS = {
    /**
     * Карта обработчиков нажатия клавиш.
     * Карта разделена на зоны, определяющие нажатия функциональных клавиш.
     * @public
     * @type {Object}
     */
    map: {
      value: {
        'ctrl': {
          'alt': {
            'shift': {}
          },
          'shift': {}
        },
        'alt': {
          'shift': {}
        },
        'shift': {}
      }
    }
  };

  Y.extend(HotKey, Y.Widget, {
    bindUI: function(){
      var context = this;
      Y.Node.one('body').on('keypress', function(e){
        var fun = context.get('map');

        if(e.ctrlKey){
          fun = fun['ctrl'];
        }

        if(e.altKey){
          fun = fun['alt'];
        }

        if(e.shiftKey){
          fun = fun['shift'];
        }

        if(fun[e.keyCode]){
          if(fun[e.keyCode](e)){
            e.preventDefault();
          }
        }
      });
    },

    /**
     * Метод добавляет обработчик для клавиши.
     * В случае, если для данной клавиши уже установлен обработчик, он заменяется.
     * @param {boolean} ctrl Признак нажатия клавиши ctrl.
     * @param {boolean} alt Признак нажатия клавиши alt.
     * @param {boolean} shift Признак нажатия клавиши shift.
     * @param {string} key Имя нажатой клавиши в соответствии с KEYS_CODE.
     * @param {function} callback Функция-обработчик.
     * @public
     * @function
     */
    addCommand: function(ctrl, alt, shift, key, callback){
      var fun = this.get('map');

      if(ctrl){
        fun = fun['ctrl'];
      }

      if(alt){
        fun = fun['alt'];
      }

      if(shift){
        fun = fun['shift'];
      }

      if(HotKey.KEYS_CODE[key] !== undefined){
        var code = HotKey.KEYS_CODE[key];
        if((typeof code) == 'number'){
            fun[code] = callback;
        }
        else if((typeof code) == 'object'){
          fun[code[0]] = callback;
          for(var i = 1; i < code.length; i++){
            fun[code[i]] = fun[code[0]];
          }
        }
      }
    }
  });

  Y.namespace('PJS.widgets').HotKey = HotKey;
}, '1.0', {requires: ['widget', 'node', 'event']});