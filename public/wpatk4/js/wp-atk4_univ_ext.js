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
    atkWpMessage: function(id, effect, msg){
        var html, dest;

        getDestination = function(){
            var container = $('#' + id).parents('.atk-wp-body').find('.atkwp-notice');
            if ( ! container.length ) {
                //check for a shortcode output
                container = $('#' + id).find('.atkwp-notice');
            }
            return container;
        };

        html = $('<div class="atk-layout-row" style="position: absolute; z-index: 1000;"></div>')
                .append( $('<div class="atk-effect-' + effect + ' atk-cells atk-box-small"></div>')
                    .append( $('<div class="atk-cell atk-jackscrew"></div>')
                            .append( $('<i class="icon-info"></i>'))
                            .append( $('<span>' + msg + '</span>')))
                    .append( $('<div class="atk-cell"></div>')
                            .append( $('<a href="javascript: void()" ></a>'))
                                .click(function(e){e.preventDefault();html.remove();})
                            .append( $('<i class="icon-cancel"></i>'))
                            )
                    );

        if( dest = getDestination() ){
            html.prependTo(dest);
        }else{
            alert(msg);
        }
        setTimeout(function() { html.remove();},8000);
    }
},$.univ._import);