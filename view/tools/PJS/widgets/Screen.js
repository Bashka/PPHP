/**
 * @namespace PPHP\view\tools\PJS\widgets\Screen
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.widgets.Screen', function(Y){
  /**
   * Данный виджет представляет внедренный экран.
   * После рендеринга виджета выполняется трансформация полученного экрана и рендеринг его внедренных экранов.
   * @param {Object} cnf Параметры класса.
   * Обязательными параметрами являются:
   * - module - имя модуля внедренного экрана;
   * - screen - имя экрана.
   * @constructor
   * @this {Screen}
   */
  function Screen(cnf){
    Screen.superclass.constructor.apply(this, arguments);

    if(cnf === undefined || cnf.module === undefined || cnf.screen === undefined){
      throw new Error('Невозможно инициализировать виджет [PJS.widgets.Screen]. Отсутствует обязательный параметр конфигурации [module, screen].');
    }
  }

  Screen.NAME = 'screen';
  Screen.ATTRS = {
    /**
     * Имя модуля экрана.
     * @public
     * @type {String}
     */
    module: {
      writeOnce: 'initOnly',
      validator: function(val){
        return (typeof(val) == 'string' && val.length > 0);
      }
    },
    /**
     * Имя экрана.
     * @public
     * @type {String}
     */
    screen: {
      writeOnce: 'initOnly',
      validator: function(val){
        return (typeof(val) == 'string' && val.length > 0);
      }
    },
    /**
     * Расположение экрана относительно корня сайта.
     * @public
     * @type {String}
     */
    location: {
      valueFn: function(){
        return '/PPHP/view/screens/'+this.get('module')+'/'+this.get('screen')+'/';
      },
      writeOnce: 'readOnly'
    },
    /**
     * Расположение файла контроллера экрана относительно каталога экранов
     * @public
     * @type {String}
     */
    locationController: {
      valueFn: function(){
        return this.get('module')+'/'+this.get('screen')+'/'+this.get('screen')+'.js';
      },
      writeOnce: 'readOnly'
    },
    /**
     * Определение наличия файла структуры экрана.
     * @public
     * @type {bool}
     */
    hasHTML: {
      value: true,
      writeOnce: 'initOnly'
    },
    /**
     * Определение наличия файла стиля экрана.
     * @public
     * @type {bool}
     */
    hasCSS: {
      value: false,
      writeOnce: 'initOnly'
    },
    /**
     * Определение наличия контроллера экрана.
     * @public
     * @type {bool}
     */
    hasController: {
      value: true,
      writeOnce: 'initOnly'
    },
    transformNode: {}
  };

  Y.extend(Screen, Y.Widget, {
    /**
     * Метод подключает файл стиля экрана.
     * @private
     * @function
     */
    _loadCSS: function(){
      Y.Get.css(this.get('location')+this.get('screen')+'.css', {
        timeout:500
      });
    },

    /**
     * Метод формирует и возвращает внедренные узлы.
     * Внедренными узлами являются узлы с установленым атрибутом dHandling.
     * Атрибут dHandling может включать следующие значения, перечисленные через точку с запятой:
     * - filling: <name> - внедрение узла в свойство filling контроллера;
     * - init: <name> - вызов метода name контроллера с передачей ему узла для инициализации;
     * - listen: <name> - вызов метода name контроллера с передачей ему узла для установки слушателей;
     * - access: <name> - вызов метода name контроллера с передачей ему узла для определения доступа. Если метод вернет false, узел будет удален из экрана.
     * @private
     * @function
     */
    _getHandlingNodes: function(){
      var handlingNodes = this.get('boundingBox').all('[dHandling]'),
        result = [[], [], [], []];
      handlingNodes.each(function(node){
        var properties = node.getAttribute('dHandling').split(';');
        for(var i in properties){
          var property = properties[i].split(':');
          switch (property[0].trim()){
            case 'filling':
              result[0][property[1].trim()] = node;
              break;
            case 'init':
              result[1][property[1].trim()] = node;
              break;
            case 'listen':
              result[2][property[1].trim()] = node;
              break;
            case 'access':
              result[3][property[1].trim()] = node;
              break;
          }
        }
      });
      return result;
    },

    /**
     * Метод подключает и инициализирует контроллер экрана.
     * @private
     * @function
     */
    _loadController: function(){
      // Информирование песочницы о расположении контроллера
      var module = 'PJS.screens.'+this.get('module')+'.'+this.get('screen')+'.Controller',
        screen = this,
        handlingNodes = this._getHandlingNodes();
      Y.config.groups.PJS_controllers.modules[module] = {
        path: this.get('locationController')
      };

      // Создание и инициализация контроллера
      Y.use(module, function(){
        var controller = eval('Y.'+module);
        controller = new controller({
          screen: screen,
          filling: handlingNodes[0]
        });

        // Разграничение доступа
        for(var access in handlingNodes[3]){
          if((typeof controller[access]) == 'function'){
            if(!controller[access](handlingNodes[3][access])){
              handlingNodes[3][access].remove();
            }
          }
          else{
            Y.log('Невозможно определить права доступа для узла. Отсутствует требуемый метод контроллера ['+screen.get('module')+'.'+screen.get('screen')+'::'+access+'].');
          }

        }

        // Инициализация экрана
        controller.initScreen();

        // Инициализация узлов
        for(var init in handlingNodes[1]){
          if((typeof controller[init]) == 'function'){
            controller[init](handlingNodes[1][init]);
          }
          else{
            Y.log('Невозможно инициализировать узел. Отсутствует требуемый метод контроллера ['+screen.get('module')+'.'+screen.get('screen')+'::'+init+'].');
          }
        }

        // Установка слушателей узлов
        for(var listener in handlingNodes[2]){
          if((typeof controller[listener]) == 'function'){
            controller[listener](handlingNodes[2][listener]);
          }
          else{
            Y.log('Невозможно слушать узел. Отсутствует требуемый метод контроллера ['+screen.get('module')+'.'+screen.get('screen')+'::'+listener+'].');
          }
        }
      });
    },

    /**
     * Метод загружает внедренный экран.
     * Метод генерирует событие PJS.widgets.Screen:DOMReady на объекте, что информирует о завершении загрузки DOM экрана.
     */
    renderUI: function(){
      //this.get('boundingBox').screen = this;

      // Визуализация виджета после полной загрузки и инициализации контроллера
      this.on('PJS.widgets.Screen:ControllerReady', function(){
        this.get('boundingBox').setStyle('visibility', 'visible')
      });

      var object = this;
      this.get('boundingBox').setStyle('visibility', 'hidden');
      if(this.get('hasHTML')){
        // Загрузка DOM. Отключение видимости виджета до загрузки и инициализации контроллера
        this.get('contentBox').load(this.get('location')+this.get('screen')+'.html', function(){
          object.fire('PJS.widgets.Screen:DOMReady');

          // Трансформация компонентов
          Y.PJS.services.Transformer.transform(object.get('contentBox'));
        });
      }
      else{
        this.fire('PJS.widgets.Screen:DOMReady');
      }

      // Загрузка контроллера
      // Выполняется после полной трансформации узлов экрана
      var syncFun = function(data){
        if(data === object.get('transformNode')){
          Y.PJS.services.Transformer.detach('PJS.services.Transformer:renderComplete', syncFun, object);

          // Загрузка контроллера
          if(object.get('hasController')){
            object._loadController();
          }
        }
      };
      Y.PJS.services.Transformer.on('PJS.services.Transformer:renderComplete', syncFun, this);

      // Загрузка стиля
      if(this.get('hasCSS')){
        this._loadCSS();
      }
    }
  });

  Y.namespace('PJS.widgets').Screen = Screen;
}, '1.0', {requires: ['widget', 'node', 'node-load', 'get', 'PJS.classes.Controller', 'PJS.services.Transformer', 'event']});