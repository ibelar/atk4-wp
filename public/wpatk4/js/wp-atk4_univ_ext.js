/**
 * Created by abelair on 2015-08-31.
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
        //var html = $('<div>', {class: "notice " + type }).append($('<p>').html( msg ));
        var html;

        switch ( type ){
            case 'success':
                html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000; opacity: 0.8">\
                    <div class="atk-swatch-green atk-cells atk-padding-small">\
                      <div class="atk-cell atk-jackscrew"><i class="icon-info"></i>&nbsp;<span></span></div>\
                      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
                    </div>\
                  </div>');
                break;
            case 'error':
                html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000;  opacity: 0.8">\
                    <div class="atk-swatch-red atk-cells atk-padding-small">\
                      <div class="atk-cell atk-jackscrew"><i class="icon-attention"></i>&nbsp;<span></span></div>\
                      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
                    </div>\
                  </div>');
                break;
        }
        html.find('span').text(msg);
        html.find('.do-close').click(function(e){e.preventDefault();html.remove();});
        var dest = $('#' + id).parents('.atk-wp-body').find('.atkwp-notice');
        if(dest.length){
            html.prependTo(dest);
        }else{
            alert(msg);
        }
        setTimeout(function() { html.remove();},8000);
    },
},$.univ._import);