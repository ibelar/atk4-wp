/**
 * Created by abelair on 2015-08-26.
 * Overwrite and Redefine atk4.get function.
 * Ensure that the wp-ajax is properly setup by setting our ajax action when need.
 */
$.extend($.atk4,{
    get: function(url, data, callback, load_end_callback, post){
        var self=this;
        var panel = $("#atk-wp-content").data('atkPanel');
        var ajaxAction = $("#atk-wp-content").data('atkAction');

        if($.isFunction(data)){
            callback=data;
            // data argument action need to be set for wp-ajax to work.
            // if data is null, simply set it to a bogus action.
            // otherwise, certain plugin might throw an error. (ex: WPML)
            data={'action' : 'atkFunction'};
        } else if ( data === null || data === undefined){
            //Setup wpajax with our action.
            data = {'action' : ajaxAction, 'atkpanel' : panel};
        } else if ($.isArray(data)){
            //add our action to array of data
            data.push({ 'name' : 'action', 'value' : ajaxAction}, { 'name' : 'atkpanel', 'value' : panel});
        } else {
            //finally add our action as a url param.
            var param = {'action' : ajaxAction, 'atkpanel' : panel};
            url = $.atk4.addArgument(url, jQuery.param( param ));
        }
        var timeout=setTimeout(function(){
            self._stillLoading(url);
        },2000);
        if(typeof(url)==="object" && url[0]){
            url=$.atk4.addArgument(url);
        }
        if(typeof(url)==="string"){
            url={url:url};
        }

        // Another file is being loaded.
        this.loading++;
        return $.ajax($.extend({
            type: post?"POST":"GET",
            dataType: 'html',
            data: data,
            // We tell the backend that we will verify output for "TIMEOUT" output
            beforeSend: function(xhr){xhr.setRequestHeader('X-ATK4-Timeout', 'true');},

            success: function(res){
                clearTimeout(timeout);
                $.atk4._refreshTimeout();
                if(load_end_callback) {
                    load_end_callback();
                }
                if($.atk4._checkSession(res) && callback) {
                    callback(res);
                }
                if(!--$.atk4.loading){
                    $.atk4._readyExec();
                }
            },
            error: function(a,b,c){
                clearTimeout(timeout);
                $.atk4._refreshTimeout();
                if(load_end_callback) {
                    load_end_callback();
                }
                $.atk4._ajaxError(url,a,b,c);
                if(!--$.atk4.loading){
                    $.atk4._readyExec();
                }
                // kill readycheck handlers by not reducing
                // the counter.
            }
        },url));
    },
})(jQuery);