/**
 * @namespace PPHP\view\tools\PJS\services\Transformer
 * @author Artur Sh. Mamedbekov
 */
YUI.add('PJS.services.Transformer', function(Y){
  /**
   * Данная служба позволяет преобразовать представление.
   * Служба выполняет следующие преобразования:
   * - Визуализирует виджеты. Для указания узла-виджета используется атрибут узла dWidget, в котором указывается имя виджета;
   */
  Y.augment(Y.namespace('PJS.services').Transformer = (function(){
    /**
     * Метод получает и преобразует атрибут dWidgetProperties в объект конфигурации виджета.
     * @public
     * @function
     * @param {Node} widget Визуализируемый виджет.
     * @return {Object} Объект конфигурации виджета.
     */
    function transformWidgetProperties(widget){
      var properties = widget.getAttribute('dWidgetProperties');
      if(properties !== ''){
        var result = {};
        properties = properties.split(';');
        for(var i in properties){
          var property = properties[i].split(':');
          property[0] = property[0].trim();
          property[1] = property[1].trim();
          result[property[0]] = (property[1] == 'true')? true: ((property[1] == 'false')? false: property[1]);
        }
        return result;
      }
      else{
        return {};
      }
    }

    /**
     * Метод визуализируем виджет типа PJS.widgets.LoadingIndicator.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderLoadingIndicator(widget){
      Y.use('PJS.widgets.LoadingIndicator', function(){
        widget.widget = new Y.PJS.widgets.LoadingIndicator({
          boundingBox: widget
        }).render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа Console.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderConsole(widget){
      Y.use('console', 'dd-plugin', function(){
        var console = new Y.Console(transformWidgetProperties(widget)).render();
        widget.widget = console;
        console.plug(Y.Plugin.Drag);
        console.dd.addHandle('.yui3-console-hd');
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа Calendar.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderCalendar(widget){
      Y.use('calendar', function(){
        var properties = transformWidgetProperties(widget);
        // Обработка "особых" свойств
        if(properties.date !== undefined){
          properties.date = properties.date.split('.');
          properties.date = new Date(properties.date[0], properties.date[1], properties.date[2]);
        }
        properties.boundingBox = widget;

        widget.widget = new Y.Calendar(properties).render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа TabView.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderTabView(widget){
      Y.use('tabview', function(){
        var properties = transformWidgetProperties(widget);
        properties.srcNode = widget;
        widget.widget = new Y.TabView(properties).render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа AutoComplete.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderAutoComplete(widget){
      Y.use('autocomplete', function(){
        var properties = transformWidgetProperties(widget);
        if(properties.source !== undefined){
          properties.source = properties.source.split(',');
        }
        if(properties.resultFilters !== undefined){
          Y.use('autocomplete-filters');
          properties.resultFilters = properties.resultFilters.split(',');
        }
        if(properties.resultHighlighter !== undefined){
          Y.use('autocomplete-highlighters');
          properties.resultHighlighter = properties.resultHighlighter.split(',');
        }
        widget.widget = widget.plug(Y.Plugin.AutoComplete, properties).ac;
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа DataTable.
     * Узел виджета должен включать:
     * - div узел, содержащий множество div узлов, описывающих колонки таблицы. Текстовое содержимое этих узлов представляет имя колонки и атрибут dWidgetProperties ее свойства.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderDataTable(widget){
      Y.use('datatable', function(){
        var properties = transformWidgetProperties(widget);
        properties.boundingBox = widget;

        // Формирование колонок
        properties.columns = [];

        // Обработка свойства selected
        if(properties.selected){
          properties.columns.push({
            key: 'selected',
            formatter: '<input type="checkbox" name="item" value="{value}">',
            label: '<input type="checkbox" value="all">',
            allowHTML: true
          });
        }

        // Формирование обычных колонок
        widget.get('children').item(0).all('div').each(function(column){
          var col = transformWidgetProperties(column);
          col.key = column.getHTML();
          properties.columns.push(col);
        });

        widget.empty();
        widget.widget = new Y.DataTable(properties).render();

        // Обработка свойства selected
        if(properties.selected){
          widget.delegate('click', function (e) {
            this.widget.get('contentBox').all('.yui3-datatable-col-selected input[name]').set('checked', e.currentTarget.get('checked'));
          }, '[value=all]', widget);
        }
      });
    }

    /**
     * Метод визуализируем виджет типа PJS.widgets.Screen.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderScreen(widget){
      Y.use('PJS.widgets.Screen', function(){
        var properties = transformWidgetProperties(widget);
        properties.boundingBox = widget;

        widget.widget = new Y.PJS.widgets.Screen(properties);
        widget.widget.render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа Upload для загрузки одного файла.
     * Обязательными свойствами виджета являются:
     * - module - имя модуля-обработчика;
     * - action - имя метода, отвечающего за обработку загружаемого файла.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderUploaderHTML(widget){
      Y.use('PJS.widgets.UploaderHTML', function(){
        var properties = transformWidgetProperties(widget);
        properties.boundingBox = widget;

        widget.widget = new Y.PJS.widgets.UploaderHTML(properties);
        widget.widget.render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет типа node-menunav.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderMenuNav(widget){
      Y.use('node-menunav', function(){
        var properties = transformWidgetProperties(widget);

        widget.addClass('yui3-menu');
        if(properties.orientation === undefined || properties.orientation == 'horizontal'){
          widget.addClass('yui3-menu-horizontal');
        }
        else{
          widget.addClass('yui3-menu-vertical');
        }

        widget.plug(Y.Plugin.NodeMenuNav);
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    function _renderContextMenu(widget){
      Y.use('gallery-contextmenu-view', function(){
        var properties = transformWidgetProperties(widget);
        properties.trigger = {
          node:   widget
        };
        if(properties.target === undefined){
          properties.trigger.target = widget.get('tagName');
        }
        else{
          properties.trigger.target = properties.target;
        }
        properties.menuItems = properties.items.split(',');

        widget.widget = new Y.ContextMenuView(properties);
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод визуализируем виджет неопределенного типа.
     * Для удачной визуализации необходимо, чтобы адрес модуля соответствовал его имени.
     * @private
     * @function
     * @param {Node} widget Визуализируемый виджет.
     */
    function _renderDefault(widget){
      var widgetName = widget.getAttribute('dWidget');
      widgetName = widgetName.split('-').join('.');

      // Определение локальных виджетов
      if(Y.config.groups.PJS_widgets.modules[widgetName] === undefined){
        var cwn = widgetName.split('.');
        Y.config.groups.PJS_widgets.modules[widgetName] = {
          path: '../../../'+cwn[1]+'/'+cwn[2]+'/'+cwn[3]+'/'+cwn[4]+'.js'
        };
      }

      Y.use(widgetName, function(){
        var properties = transformWidgetProperties(widget);
        if(widget.get('children').size() == 0){
          properties.boundingBox = widget;
        }
        else{
          properties.srcNode = widget;
        }


        var widgetClass = eval('Y.'+widgetName);
        widget.widget = new widgetClass(properties);
        widget.widget.render();
        Y.PJS.services.Transformer.fire('PJS.services.Transformer:widgetRender', widget);
      });
    }

    /**
     * Метод маршуритизирует визуализацию виджетов.
     * @private
     * @function
     * @param {NodeList} widgetNodes Визуализируемые виджеты.
     */
    function _renderWidgets(widgetNodes){
      widgetNodes.each(function(widget){
        switch(widget.getAttribute('dWidget')){
          case 'PJS-widgets-LoadingIndicator':
            _renderLoadingIndicator(widget);
            break;
          case 'Console':
            _renderConsole(widget);
            break;
          case 'Calendar':
            _renderCalendar(widget);
            break;
          case 'TabView':
            _renderTabView(widget);
            break;
          case 'AutoComplete':
            _renderAutoComplete(widget);
            break;
          case 'DataTable':
            _renderDataTable(widget);
            break;
          case 'PJS-widgets-Screen':
            _renderScreen(widget);
            break;
          case 'UploaderHTML':
            _renderUploaderHTML(widget);
            break;
          case 'node-menunav':
            _renderMenuNav(widget);
            break;
          case 'contextmenu':
            _renderContextMenu(widget);
            break;
          default:
            _renderDefault(widget);
        }
        widget.fire('PJS.services.Transformer:widgetRender');
      });
    }

    return {
      getWidgetProperties: transformWidgetProperties,

      /**
       * Метод преобразовывает представление в переданных узлах.
       * Метод вызывает следующие события на объекте:
       * - PJS.services.Transformer:widgetRender - рендеринг виджета завершен.
       * @public
       * @function
       * @param {Node|NodeList} widgets Визуализируемый виджет или список виджетов.
       * @param {function} callback [optional] Функция, вызываемая после рендеринга всех переданных виджетов.
       * @param {object} context [optional] Объект, от имени которого вызывается функция обратнго вызова callback.
       */
      transform: function(widgets, callback, context){
        context = context || window;

        if(widgets instanceof Y.Node){
          widgets = new Y.NodeList(widgets);
        }

        if((typeof callback) == 'function'){
          var widgetsCount = widgets.size(),
            completeWidgets = 0;
          if(widgetsCount == 0){
            callback.apply(context);
          }
          else{
            widgets.each(function(node){
              node.once('PJS.services.Transformer:widgetRender', function(){
                completeWidgets++;
                if(completeWidgets == widgetsCount){
                  callback.apply(context);
                }
              });
            });
          }
        }

        _renderWidgets(widgets);
      }
    }
  })(), Y.EventTarget);
}, '1.0', ['node', 'event']);