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
     * Логический флаг, определяющий готовность DOM экрана.
     * @public
     * @type {boolean}
     */
    _isDOMReady: {
      writeOnce: true
    },
    /**
     * Логический флаг, определяющий завершение трансформации DOM экрана.
     * @public
     * @type {boolean}
     */
    _isDOMTransform: {
      writeOnce: true
    },
    /**
     * Логический флаг, определяющий инстанциацию контроллера экрана.
     * @public
     * @type {boolean}
     */
    _isControllerCreate: {
      writeOnce: true
    },
    /**
     * Логический флаг, определяющий инициализацию контроллера экрана.
     * @public
     * @type {boolean}
     */
    _isControllerInit: {
      writeOnce: true
    },
    /**
     * Логический флаг, определяющий загрузку и инициализацию контроллеров вложенных экранов.
     * @public
     * @type {boolean}
     */
    _isComponentsControllersInit: {
      writeOnce: true
    },
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
     * Контроллер экрана.
     * @public
     * @type {PJS.classes.Controller}
     */
    controller: {
      writeOnce: true
    },
    /**
     * Расположение экрана относительно корня сайта.
     * @public
     * @type {String}
     */
    location: {
      valueFn: function(){
        return '/PPHP/view/screens/' + this.get('module') + '/' + this.get('screen') + '/';
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
        return this.get('module') + '/' + this.get('screen') + '/' + this.get('screen') + '.js';
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
    /**
     * Определение наличия визуальной панели управления модулем.
     * @public
     * @type {bool}
     */
    hasAdminPanel: {
      value: false
    },
    /**
     * Множество внедренных компонентов экрана.
     * @public
     * @type {Object}
     */
    handling: {},
    /**
     * Множество внедренных ссылок на компоненты экрана.
     * @public
     * @type {Object}
     */
    filling: {}
  };

  Y.extend(Screen, Y.Widget, {
    /**
     * Метод добавляет инструмент управления экраном для администратора.
     * @private
     * @function
     */
    _addAdminMenu: function(){
      if(this.get('hasAdminPanel') && (Y.PJS.services.User.hasRole('Moderator role') || Y.PJS.services.User.hasRole('Administrator role'))){
        var adminButton = Y.Node.create('<input type="button" title="' + this.get('module') + ':' + this.get('screen') + '" class="yui3-button" value="edit" style="width: 28px; height: 24px; float: right"/>'),
        context = this;
        Y.use('panel', function(){
          var panel = new Y.Panel({
            bodyContent: '<div dWidget="PJS-widgets-Screen" dWidgetProperties="module: '+context.get('module')+'; screen: admin; navigate: horizontal; hasAdminPanel: false" ></div>',
            zIndex: 6,
            width: '100%',
            modal: true
          });
          panel.render();
          Y.PJS.services.Transformer.transform(panel.get('bodyContent'));
          panel.hide();
          adminButton.panel = panel;
        });

        this.get('contentBox').one('*').insert(adminButton, 'before');


        adminButton.on('mouseover', function(){
          context.get('boundingBox').setStyle('border', '1px solid red');
        });
        adminButton.on('mouseout', function(){
          context.get('boundingBox').setStyle('border', 'none');
        });
        adminButton.on('click', function(){
          this.panel.show();
        });
      }
    },

    /**
     * Метод подключает файл стиля экрана, если таковой имеется у экрана.
     * @private
     * @function
     */
    _loadCSS: function(){
      if(this.get('hasCSS')){
        Y.Get.css(this.get('location') + this.get('screen') + '.css', {
          timeout: 500
        });
      }
    },

    /**
     * Метод загружает DOM экрана, если для экрана задан файл структуры.
     * Метод генерирует событие PJS.widgets.Screen:DOMReady после загрузки DOM экрана.
     * @private
     * @function
     */
    _loadDOM: function(){
      if(this.get('hasHTML')){
        var context = this;
        this.get('contentBox').load(this.get('location') + this.get('screen') + '.html', function(){
          context._addAdminMenu();
          context.set('_isDOMReady', true);
          context.fire('PJS.widgets.Screen:DOMReady');
        });
      }
      else{
        var cb = this.get('contentBox');
        // Перенос компонентов экрана в content box
        this.get('boundingBox').all('> *').each(function(node){
          if(node != cb){
            cb.append(node);
          }
        });
        this._addAdminMenu();
        this.set('_isDOMReady', true);
        this.fire('PJS.widgets.Screen:DOMReady');
      }
    },

    /**
     * Метод трансформирует DOM экрана.
     * Метод генерирует событие PJS.widgets.Screen:DOMTransform после трансформации DOM экрана.
     * @private
     * @function
     */
    _transformDOM: function(){
      if(this.get('hasHTML')){
        var dwidgets = this.get('boundingBox').all('[dWidget]'),
          widgets = [];

        dwidgets.each(function(node){
          if(node.getAttribute('dWidget') != 'PJS.widgets.Screen'){
            widgets.push(node);
          }
        });
        widgets = new Y.NodeList(widgets);

        Y.PJS.services.Transformer.transform(widgets, function(){
          this.set('_isDOMTransform', true);
          this.fire('PJS.widgets.Screen:DOMTransform');
        }, this);
      }
      else{
        this.set('_isDOMTransform', true);
        this.fire('PJS.widgets.Screen:DOMTransform');
      }
    },

    /**
     * Метод загружает и инстанциирует контроллер экрана.
     * Метод генерирует событие PJS.widgets.Screen:ControllerCreate после инстанциации контроллера экрана.
     * @private
     * @function
     */
    _loadController: function(){
      if(this.get('hasController')){
        // Информирование песочницы о расположении контроллера
        var module = 'PJS.screens.' + this.get('module') + '.' + this.get('screen') + '.Controller',
          context = this,
          controllerParams = Y.PJS.services.Transformer.getWidgetProperties(this.get('boundingBox'));
        controllerParams['screen'] = this;

        // Определение адреса контроллера
        Y.config.groups.PJS_controllers.modules[module] = {
          path: this.get('locationController')
        };

        // Создание контроллера
        Y.use(module, function(){
          var controller = eval('Y.' + module);
          controller = new controller(controllerParams);
          context.set('controller', controller);
          context.set('_isControllerCreate', true);
          context.fire('PJS.widgets.Screen:ControllerCreate');
        });
      }
      else{
        this.set('_isControllerCreate', true);
        this.fire('PJS.widgets.Screen:ControllerCreate');
      }
    },

    /**
     * Метод инициализирует контроллер экрана.
     * Метод генерирует событие PJS.widgets.Screen:ControllerInit после инициализации контроллера экрана.
     * @private
     * @function
     */
    _initController: function(){
      if(this.get('hasController')){
        var controller = this.get('controller'),
          context = this,
          handlingNodes = this.get('handling');

        // Передача внедренных узлов контроллеру
        controller.set('filling', this.get('filling'));

        // Обработка узлов
        handlingNodes.each(function(handlingNode){
          // Разграничение видимости
          var role = handlingNode.getData('PJS.handling.visible');
          if(role){
            if(!Y.PJS.services.User.hasRole(role)){
              handlingNode.remove();
              return false;
            }
          }

          // Разграничение доступа
          var access = handlingNode.getData('PJS.handling.access');
          if(access){
            if((typeof controller[access]) == 'function'){
              if(!controller[access](handlingNode)){
                handlingNode.remove();
                return false;
              }
            }
            else{
              Y.log('Невозможно определить права доступа для узла. Отсутствует требуемый метод контроллера [' + context.get('module') + '.' + context.get('screen') + '::' + access + '].');
            }
          }

          // Инициализация узлов
          var init = handlingNode.getData('PJS.handling.init');
          if(init){
            if((typeof controller[init]) == 'function'){
              controller[init](handlingNode);
            }
            else{
              Y.log('Невозможно инициализировать узел. Отсутствует требуемый метод контроллера [' + context.get('module') + '.' + context.get('screen') + '::' + init + '].');
            }
          }

          // Установка слушателей узлов
          var listen = handlingNode.getData('PJS.handling.listen');
          if(listen){
            if((typeof controller[listen]) == 'function'){
              controller[listen](handlingNode);
            }
            else{
              Y.log('Невозможно слушать узел. Отсутствует требуемый метод контроллера [' + context.get('module') + '.' + context.get('screen') + '::' + listen + '].');
            }
          }
        });

        // Инициализация экрана
        controller.initScreen();

        this.set('_isControllerInit', true);
        this.fire('PJS.widgets.Screen:ControllerInit');
      }
      else{
        this.set('_isControllerInit', true);
        this.fire('PJS.widgets.Screen:ControllerInit');
      }
    },

    /**
     * Метод запускает загрузку экранов-компонентов в данном экране.
     * Метод генерирует событие PJS.widgets.Screen:ComponentsControllersInit после загрузки и инициализации всех контроллеров экранов-компонентов.
     * @private
     * @function
     */
    _loadComponents: function(){
      var components = this.get('boundingBox').all('[dWidget=PJS-widgets-Screen]'),
        countComponents = components.size(),
        countCreateControllers = 0,
        context = this;

      Y.use('PJS.widgets.Screen', function(){
        components.each(function(screen){
          var properties = Y.PJS.services.Transformer.getWidgetProperties(screen);
          properties.boundingBox = screen;

          screen.widget = new Y.PJS.widgets.Screen(properties);
          screen.widget.once('PJS.widgets.Screen:ControllerReady', function(){
            countCreateControllers++;
            if(countCreateControllers == countComponents){
              context.set('_isComponentsControllersInit', true);
              context.fire('PJS.widgets.Screen:ComponentsControllersInit');
            }
          });
        });
      });

      components.each(function(screen){
        screen.widget.render();
      });
    },

    /**
     * Метод загружает внедренный экран.
     * Рендеринг экрана проходит следующие стадии:
     * 1. Загрузка DOM и CSS стиля экрана;
     * 2. Трансформация DOM экрана;
     * 3. Загрузка контроллера экрана;
     * 4. Если экран не имеет экранов-компонентов, то инициализация экрана;
     * 5. Иначе рендеринг экранов-компонентов и их инициализация, а после инициализация текущего экрана.
     * Данный подход позволяет инициализировать экран только после того, когда контроллеры экранов-компонентов готовы к событийному связыванию.
     */
    renderUI: function(){
      var context = this;

      // Управление видимостью экрана
      this.get('boundingBox').setStyle('visibility', 'hidden');
      this.once('PJS.widgets.Screen:ControllerInit', function(){
        // Показ экрана после инициализации его контроллера
        context.get('boundingBox').setStyle('visibility', '')
      });

      // 4 Инициализация контроллера
      if(this.get('boundingBox').all('[dWidget=PJS-widgets-Screen]').size() == 0){
        // Инициализация листового экрана

        // 4.a Инициализация контроллера после его загрузки
        this.once('PJS.widgets.Screen:ControllerCreate', function(){
          context._initController();
        });
      }
      else{
        // Инициализация не листового экрана

        // 3 Загрузка вложенных экранов
        this.once('PJS.widgets.Screen:DOMTransform', function(){
          this._loadComponents();
        });

        // 4.b Инициализация контроллера после загрузки и инициализации контроллеров вложенных экранов и собственного контроллера
        this.once('PJS.widgets.Screen:ComponentsControllersInit', function(){
          if(context.get('_isControllerCreate')){
            context._initController();
          }
        });

        this.once('PJS.widgets.Screen:ControllerCreate', function(){
          if(context.get('_isComponentsControllersCreate')){
            context._initController();
          }
        });
      }

      // 3 Загрузка контроллера после трансформации DOM
      this.once('PJS.widgets.Screen:DOMTransform', function(){
        context._loadController();
      });

      // 2 Трансформация DOM после загрузки
      this.once('PJS.widgets.Screen:DOMReady', function(){
        // Предварительное формирование обрабатываемых узлов
        var filling = [],
          handlingNodes = this.get('boundingBox').all('[dHandling]'),
          context = this;

        // Формирование списка обрабатываемых узлов
        handlingNodes.each(function(node){
          var properties = node.getAttribute('dHandling').split(';');
          for(var i in properties){
            var property = properties[i].split(':');
            node.setData('PJS.handling.' + property[0].trim(), property[1].trim());
          }

          // Формирование списка внедренных узлов
          var fillingName = node.getData('PJS.handling.filling');
          if(fillingName){
            filling[fillingName] = node;
          }
        });

        this.set('handling', handlingNodes);
        this.set('filling', filling);

        context._transformDOM();
      });

      // 1 Загрузка DOM
      this._loadDOM();

      // 1 Загрузка стиля
      this._loadCSS();
    }
  });

  Y.namespace('PJS.widgets').Screen = Screen;
}, '1.0', {requires: ['widget', 'node', 'event',  'node-load', 'get', 'PJS.classes.Controller', 'PJS.services.Transformer', 'PJS.services.User']});
