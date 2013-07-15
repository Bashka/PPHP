/**
 * @namespace PPHP\view\tools\PJS\widgets\UploaderHTML
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.widgets.UploaderHTML', function(Y){
  /**
   * Данный виджет представляет компонент загрузки файла на сервер.
   * Виджет является дочерним классом по отношению к Y.Uploader и включает все необходимые обработчики для загрузки одного файла на сервер.
   * @param {Object} cnf Параметры класса.
   * Обязательными параметрами являются:
   * - module - имя модуля-обработчика;
   * - action - имя метода, отвечающего за загрузку передаваемого файла на сервер.
   * @constructor
   * @this {UploaderHTML}
   */
  function UploaderHTML(cnf){
    UploaderHTML.superclass.constructor.apply(this, arguments);

    if(cnf === undefined || cnf.module === undefined || cnf.action === undefined){
      throw new Error('Невозможно инициализировать виджет [PJS.widgets.UploaderHTML]. Отсутствует обязательный параметр конфигурации [module, action].');
    }
  }

  UploaderHTML.NAME = 'uploaderHTML';
  /**
   * Адрес центрального контроллера.
   * @public
   * @type {String}
   */
  UploaderHTML.CENTRAL_CONTROLLER = '/PPHP/model/modules/CentralController.php';
  UploaderHTML.ATTRS = {
    /**
     * Имя модуля-обработчика.
     * @public
     * @type {String}
     */
    module: {
      writeOnce: 'initOnly'
    },
    /**
     * Имя метода, отвечающего за загрузку передаваемого файла на сервер.
     * @public
     * @type {String}
     */
    action: {
      writeOnce: 'initOnly'
    }
  };

  Y.extend(UploaderHTML, Y.Uploader, {
    bindUI: function(){
      UploaderHTML.superclass.bindUI.apply(this, arguments);

      this.on('fileselect', function(){
        this.get('boundingBox').hide();
        this.upload(this.get("fileList")[0], Y.PJS.widgets.UploaderHTML.CENTRAL_CONTROLLER, {module: this.get('module'), active: this.get('action')});
      });

      this.on('uploadcomplete', function(){
        this.set("fileList", []);
        this.get('boundingBox').show();
      });
    }
  });

  Y.namespace('PJS.widgets').UploaderHTML = UploaderHTML;
}, '1.0', {requires: ['widget', 'node', 'uploader', 'event']});