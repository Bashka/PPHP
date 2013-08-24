/**
 * @namespace PPHP\view\tools\PJS\classes\Controller
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.classes.Controller', function(Y){
  /**
   * Данный класс представляет контроллер экрана и позволяет инициализировать экран и его компоненты.
   * Класс должен быть расширен конкретными контроллерами экранов с добавлением метода initScreen, который должен инициализировать экран.
   * @param {Object} cnf Параметры класса.
   * @constructor
   * @this {Controller}
   */
  function Controller(cnf){
    Controller.superclass.constructor.apply(this, arguments);

    if(cnf === undefined || cnf.screen === undefined){
      throw new Error('Невозможно инициализировать виджет [PJS.classes.Controller]. Отсутствует обязательный параметр конфигурации [screen].');
    }
  }

  Controller.NAME = 'controller';
  Controller.ATTRS = {
    /**
     * Ссылка на экран.
     * @public
     * @type {PJS.widgets.Screen}
     */
    screen: {
      writeOnce: 'initOnly',
      validator: function(val){
        return val instanceof Y.PJS.widgets.Screen;
      }
    },
    /**
     * Массив внедренных компонентов экрана с атрибутом dHandling:get.
     * Ключами данного массива являются значения атрибута dHandling:filling.
     * @public
     * @type {Node[]}
     */
    filling: {
      writeOnce: true
    },
    /**
     * Логическая переменная, определяющая завершение инициализации контроллера.
     * @public
     * @type {bool}
     */
    isComplete: {
      value: false
    }
  };

  Y.extend(Controller, Y.Base, {
    /**
     * Метод информирует экран о завершении инициализации контроллера и делает экран видимым.
     * Если данный метод не будет вызван, экран будет визуализирован после асинхронного вызова методов инициализации.
     * Данный метод работает только при первом вызове.
     * @private
     * @function
     */
    _initComplete: function(){
      if(!this.get('isComplete')){
        this.get('screen').fire('PJS.widgets.Screen:ControllerReady');
        this.set('isComplete', true);
      }
    },

    query: function(mehtod, options){
      var screen = this.get('screen');
      Y.PJS.services.Query.query(screen.get('module'), mehtod, options);
    },

    fireScreen: function(type, data){
      this.get('screen').fire(type, data);
    }
  });

  Y.namespace('PJS.classes').Controller = Controller;
}, '1.0', {requires: ['base', 'PJS.services.Query']});