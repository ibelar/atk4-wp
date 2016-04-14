/* =====================================================================
 * Atk4-wp => An Agile Toolkit PHP framework interface for WordPress.
 *
 * This interface enable the use of the Agile Toolkit framework within a WordPress site.
 *
 * Please note that atk or atk4 mentioned in comments refer to Agile Toolkit or Agile Toolkit version 4.
 * More information on Agile Toolkit: http://www.agiletoolkit.org
 *
 * Author: Alain Belair
 * Licensed under MIT
 * =====================================================================*/
/**
 * Univ Extension.
 */
$.each({
    message: function(msg,html){
        html.find('span').text(msg);

        html.find('.do-close').click(function(e){e.preventDefault();html.remove();});

        var dest=$(".atk-wp-body");
        if(dest.length){
            html.prependTo(dest);
            return html;
        }else{
            alert(msg);
            return false;
        }
    },
    atkWpMessage: function(id, type, msg){
        var html, dest;

        getDestination = function(){
            var container = $('#' + id).parents('.atk-wp-body').find('.atkwp-notice');
            if ( ! container.length ) {
                container = $('#' + id).find('.atkwp-notice');
            }
            return container;
        };



        switch ( type ){
            case 'success':
                html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000;">\
                    <div class="atk-effect-success atk-cells atk-box-small">\
                      <div class="atk-cell atk-jackscrew"><i class="icon-info"></i>&nbsp;<span></span></div>\
                      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
                    </div>\
                  </div>');
                break;
            case 'error':
                html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000;">\
                    <div class="atk-effect-danger atk-cells atk-box-small">\
                      <div class="atk-cell atk-jackscrew"><i class="icon-attention"></i>&nbsp;<span></span></div>\
                      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
                    </div>\
                  </div>');
                break;
            case 'warning':
                html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000;">\
                    <div class="atk-effect-warning atk-cells atk-box-small ">\
                      <div class="atk-cell atk-jackscrew"><i class="icon-attention"></i>&nbsp;<span></span></div>\
                      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
                    </div>\
                  </div>');
                break;
        }
        html.find('span').text(msg);
        html.find('.do-close').click(function(e){e.preventDefault();html.remove();});

        if( dest = getDestination() ){
            html.prependTo(dest);
        }else{
            alert(msg);
        }
        setTimeout(function() { html.remove();},8000);
    },
},$.univ._import);