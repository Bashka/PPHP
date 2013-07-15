/**
 * @namespace PPHP\view\tools\PJS\widgets\LoadingIndicator
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.widgets.LoadingIndicator', function(Y){
  /**
   * Данный виджет позволяет визуализировать процесс взаимодействия с сервером по средствам службы PJS.services.Query.
   * Виджет скрывает себя, когда обмена данными с сервером нет, и показывает символ загрузки, когда клиент ожидает ответа сервера.
   * @param {Object} cnf Параметры виджета.
   * @constructor
   * @this {LoadingIndicator}
   */
  function LoadingIndicator(cnf){
    LoadingIndicator.superclass.constructor.apply(this, arguments);
  }

  LoadingIndicator.NAME = 'loadingIndicator';
  /**
   * Полный адрес изображения индикатора загрузки.
   * @public
   * @static
   * @type {string}
   */
  LoadingIndicator.IMG = '/PPHP/view/tools/PJS/widgets/loadingIndicator/loading.gif';
  LoadingIndicator.ATTRS = {
    /**
     * Число активных (ожидающих ответа сервера) запросов.
     * @public
     * @type {int}
     */
    activeRequests: {
      value: 0
    }
  };

  Y.extend(LoadingIndicator, Y.Widget, {
    renderUI: function(){
      var cb = this.get('contentBox');
      cb.setStyle('backgroundImage', 'url('+LoadingIndicator.IMG+')');
      cb.setStyle('width', 24);
      cb.setStyle('height', 24);

      var bb = this.get('boundingBox');
      bb.setStyle('width', 24);
      bb.setStyle('height', 24);
      bb.setStyle('position', 'absolute');
    },

    bindUI: function(){
      Y.PJS.services.Query.on('PJS.services.Query:QueryStart', function(){
        this.set('activeRequests', this.get('activeRequests') + 1);
        this.syncUI();
      }, this);

      Y.PJS.services.Query.on('PJS.services.Query:QueryComplete', function(){
        this.set('activeRequests', this.get('activeRequests') - 1);
        this.syncUI();
      }, this);

      this.get('boundingBox').on('mouseover', function(){
        this.setStyle('opacity', 0.2);
      });

      this.get('boundingBox').on('mouseout', function(){
        this.setStyle('opacity', 1);
      });
    },

    syncUI: function(){
      if(this.get('activeRequests') == 0){
        this.get('boundingBox').hide();
      }
      else{
        this.get('boundingBox').show();
      }
    }
  });

  Y.namespace('PJS.widgets').LoadingIndicator = LoadingIndicator;

}, '1.0', {requires: ['widget', 'node', 'PJS.services.Query', 'event']});