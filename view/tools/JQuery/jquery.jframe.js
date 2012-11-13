// jFrame
// $Revision: 1.157 $
// Author: Frederic de Zorzi
// Contact: fredz@_nospam_pimentech.net
// Revision: $Revision: 1.157 $
// Date: $Date: 2011-06-07 15:19:59 $
// Copyright: 2007-2009 PimenTech SARL
// Tags: ajax javascript pimentech english jquery


jQuery.fn.waitingJFrame = function(){
  // Overload this function in your code to place a waiting event
  // message, like :  $(this).html("<b>loading...</b>");
};

function _jsattr(elem, key){
  var res = jQuery(elem).attr(key);
  if(res == undefined){
    return function(){
      return true;
    };
  }
  if(typeof res == 'string'){
    return function(){
      eval(res);
    };
  }
  return res;
}


jQuery.fn.preloadJFrame = function(){
  if(_jsattr(this, "beforeload")() == false){
    return false;
  }
  jQuery(this).waitingJFrame();
  return true;
};

jQuery.fn.getJFrameTarget = function(){
  var target = jQuery(this).attr("target");
  if(target){
    return jQuery("#" + target);
  }
  // Returns first parent jframe element, if exists
  return jQuery(jQuery(this).parents("div[src]").get(0));
};

/*
 * Метод загружает содержимое документа, указанного в качестве атрибута src вызываемого элемента в качестве дочерних узлов вызываемого элемента.
 * Если вызываемый элемент содержит вложенные jFrame, то они так же загружаются.
 * @params string url Адрес загружаемого документа. Если данный параметр задан, то загрузка будет произведена по данному адресу, а не согласно данным атрибута src.
 * @params function callback Функция обратного вызова, которая вызывается после загрузки содержимого документа в вызываемый элементы. Данная функция вызывается от имени вызываемого элемента и принимает в качестве аргументов следующие параметры:
 * response - ответ, полученый при обращении к документу;
 * status - статус запроса;
 * xhr - XMLHttpRequest, использованный для запроса к документу.
 */
jQuery.fn.loadJFrame = function(url, callback){
  // like ajax.load, for jFrame. the onload->afterload attribute is supported
  var anchor;
  var this_callback = _jsattr(this, "afterload");
  callback = callback || function(){
  };
  url = url || jQuery(this).attr("src");
  if(url && url != "#"){
    url = url.replace(/\\/g, '/');
    if(jQuery(this).preloadJFrame() == false){
      return false;
    }
    jQuery(this).load(url,
      function(response, status, xhr){
        jQuery(this).attr("src", url);
        jQuery(this).activateJFrame();
        jQuery(this).find("div[src]").each(function(i){
          jQuery(this).loadJFrame();
        });
        this_callback();
        callback.apply(this, [response, status, xhr]);
      });
    anchor = url.lastIndexOf('#');
    if(anchor != -1){
      document.location.hash = url.substr(anchor);
    }
  }
  else{
    jQuery(this).activateJFrame();
  }
  return true;
};

jQuery.fn.activateLink = function(){
  this.unbind("click");
  this.each(function(){
    var oc = this.onclick;
    this.onclick = null;
    jQuery(this).bind("click", function(event){
      if(oc){
        if(!oc()){
          event.stopImmediatePropagation();
          return false;
        }
      }
      return true;
    });

  });
  this.click(function(){
    var target = jQuery(this).getJFrameTarget();
    if(target.length){
      var href = jQuery(this).attr("href");
      var toggle = jQuery(this).attr("toggle");
      if(href && href.indexOf('javascript:') != 0){
        if(toggle == "on"){
          target.hide();
          jQuery(this).attr("toggle", "off");
          return false;
        }
        if(toggle == "off"){
          target.show();
          jQuery(this).attr("toggle", "on");
        }
        target.loadJFrame(href);
        return false;
      }
    }
    return true;
  })
    .attr("jframe", "activated");
};

function jFrameSubmitInput(input){
  var target = jQuery(input).getJFrameTarget();
  if(target.length){
    var form = input.form;
    if(form){
      if(form.onsubmit && form.onsubmit() == false){
        return false;
      }
      if(!$.browser.msie && target.preloadJFrame() == false){
        return false;
      }

      var submit_events = jQuery(form).data("events");
      if(submit_events){
        submit_events = submit_events.submit;
        if(submit_events){
          jQuery.each(submit_events, function(i, submit){
            submit.handler();
          });
        }
      }

      jQuery(form).ajaxSubmit({
        target      :target,
        beforeSubmit:function(formData, theform, options){
          formData.push({ name:"submit", value:jQuery(input).attr("value") });
        },
        success     :function(){
          target.attr("src", jQuery(form).attr("action"));
          _jsattr(target, "afterload")();
          target.activateJFrame();
        }
      });
      return false;
    }
  }
  return true;
}

jQuery.fn.activateSubmitButton = function(){
  this.unbind("click")
    .click(function(){
      return jFrameSubmitInput(this);
    })
    .attr("jframe", "activated");
};

jQuery.fn.activateJFrame = function(){
  // Add an onclick event on all <a> and <input type="submit"> tags
  jQuery(this).find("a")
    .not("[jframe='no']")
    .not("[jframe='activated']")
    .activateLink();

  jQuery(":image,:submit,:button", this)
    .not("[jframe='no']")
    .not("[jframe='activated']")
    .activateSubmitButton();

  if($.browser.msie && $.browser.version.substr(0, 1) < 7){
    // Only for IE6 : enter key invokes submit event
    jQuery(this).find("form")
      .unbind("submit")
      .submit(function(){
        return jFrameSubmitInput(jQuery(":image,:submit,:button", this).get(0));
      });
  }
};