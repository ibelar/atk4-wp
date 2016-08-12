/**
 * Bundle atk4 js file
 *
 * In order to avoid conflict, all reference to var $ has been replace with jQuery.
 *
 * Using this file also required that other atk4 js file load via js include
 * are also using jQuery instead of var $
 *
 * ui.atk4_form.js
 * ui.atk4_grid.js
 * ui.atk4_menu.js
 * ui.atk4_reference.js
 * ui.atk4_uploader.js
 * ui.atk4_checkboxes.js
 * ui.atk4_expander.js
 * ui.atk4_richtext.js
 */

//jQuery = jQuery.noConflict();

/*====================
* start-atk4.js
====================*/
/*
 Welcome to Agile Toolkit JS framework.

 This is a main file which provides core functionality.

 ATK4 Initialisation Script.

 Usage:

 // in jQueryapp->init():

 jQuerythis->add('jUI');
 jQuerythis->js(true)->_load('start-atk4');
 */
jQuery.atk4||(function(jQuery){

    /*
     jQuery.atk4 is a function, which acts as an enhanced onReady handler.
     Syntax:

     jQuery(function(){

     jQuery.atk4.includeJS('js/mylib.js');

     jQuery.atk4(function(){
     mylib();
     });

     })


     */
    jQuery.atk4 = function(readycheck,lastcall){
        return jQuery.atk4._onReady(readycheck,lastcall);
    };

    /*
     This is initial support for univ chain. Univ chain is a part of ATK4 framework, and you
     can optionally extend it by adding your own functions
     */
    jQuery.univ = function(){
        jQuery.univ.ignore=false;
        return jQuery.univ;
    };
    jQuery.univ._import=function(name,fn){
        jQuery.univ[name]=function(){
            var ret;
            if(!jQuery.univ.ignore){
                ret=fn.apply(jQuery.univ,arguments);
            }
            return ret?ret:jQuery.univ;
        };
    };
    jQuery.fn.extend({
        univ: function(){
            var u=new jQuery.univ();
            u.jquery=this;
            return u;
        }
    });

    /*
     ATK4 Library initialisation
     */

    jQuery.extend(jQuery.atk4,{
        verison: "2.0",

        // Is this a production environment?
        production: false,

        /* It's posible that we might get called multiple times. Be aware */
        initialised: false,

        ///////////////////////// ERROR HANDLING //////////////////////////

        /*
         This function is used to display success messages. This should probably be
         redefined and should make appear pretty for the user

         Set 2nd argument to true, if it's a system message, which most likely will
         not be interesting to the user on production environment
         */
        successMessage: function(msg,system){
            if(system && this.production) {
                return;
            }
            window.console.log('Success: ',msg);
        },


        //////////////////////////////// AJAX ///////////////////////////////////
        /*
         This is lowest-level AJAX function. It will attepmt to get document from
         the server and perform some very basic validation. For instance it will
         check if server's session is expired or if it returns error.

         Other modules of ATK4 should rely on this function. If you want to
         load your own AJAX, use jQuery('..').atk4_load() instead.

         NOTE: get does not assume regarding the type of returned data.
         */
        loading: 0,	// How many files are currently being requested

        // readyList contains array of functions which must be executed
        // after loading is complete. Note that some of those functions
        // may request more files to be loaded.
        _readyList: [],

        // readyLast is a function which will be executed when all the
        // files are completed. It is used to turn off loading indicator
        _readyLast: undefined,

        // Server-side implements session timeout
        _refreshTimeout: function(){
            if(document.session_timeout){
                if(document.session_timeout_timer1){
                    clearTimeout(document.session_timeout_timer1);
                }
                if(document.session_timeout_timer2){
                    clearTimeout(document.session_timeout_timer2);
                }

                document.session_timeout_timer1=setTimeout(function(){
                    if(jQuery.univ && jQuery.univ().successMessage){
                        jQuery.univ().successMessage('Your session will expire in 1 minute due to lack of activity');
                    }

                },(document.session_timeout-1)*60*1000);

                document.session_timeout_timer2=setTimeout(function(){
                    if(jQuery.univ()){
                        jQuery.univ().dialogOK('Session timeout','You have been inactive for '+document.session_timeout+' minutes. You will need to log-in again',function(){ document.location='/'; });
                    }else{
                        window.alert('Your session have expired');
                        document.location='/';
                    }
                },(document.session_timeout)*60*1000);
            }
        },

        // If url is an object {..} then it's passed to ajax as 1st argument


        get: function(url, data, callback, load_end_callback, post){
            var self=this;
            if(jQuery.isFunction(data)){
                // data argument may be ommitted
                callback=data; data=null;
            }
            var timeout=setTimeout(function(){
                self._stillLoading(url);
            },2000);
            if(typeof(url)==="object" && url[0]){
                url=jQuery.atk4.addArgument(url);
            }
            if(typeof(url)==="string"){
                url={url:url};
            }

            // Another file is being loaded.
            this.loading++;
            return jQuery.ajax(jQuery.extend({
                type: post?"POST":"GET",
                dataType: 'html',
                data: data,
                // We tell the backend that we will verify output for "TIMEOUT" output
                beforeSend: function(xhr){xhr.setRequestHeader('X-ATK4-Timeout', 'true');},

                success: function(res){
                    clearTimeout(timeout);
                    jQuery.atk4._refreshTimeout();
                    if(load_end_callback) {
                        load_end_callback();
                    }
                    if(jQuery.atk4._checkSession(res) && callback) {
                        callback(res);
                    }
                    if(!--jQuery.atk4.loading){
                        jQuery.atk4._readyExec();
                    }
                },
                error: function(a,b,c){
                    clearTimeout(timeout);
                    jQuery.atk4._refreshTimeout();
                    if(load_end_callback) {
                        load_end_callback();
                    }
                    jQuery.atk4._ajaxError(url,a,b,c);
                    if(!--jQuery.atk4.loading){
                        jQuery.atk4._readyExec();
                    }
                    // kill readycheck handlers by not reducing
                    // the counter.
                }
            },url));
        },
        _stillLoading: function(url){
            if(this.loading){
                window.console.log('Slow loading of: ',url,'remaining:',this.loading);
            }
        },
        /*
         Use jQuery.atk4.prototype to redeclare below 2 functions to your liking.
         */
        _ajaxError: function(url,a,b,c){
            window.console.error("Failed to load file: ",url," (",a,b,c,")");
        },
        _checkSession: function(text){
            // TODO: use proper session handling instead
            if(text.substr(0,7)==="ERROR: "){
                var msg=text.substr(7);
                window.alert(msg);
                return false;
            }
            if(jQuery.trim(text)==="SESSION TIMEOUT"){
                window.alert('session has timed out');
                document.location="/";
                return false;
            }
            return true;
        },
        /*
         queues function to be executed when loading are complete.
         If "lastcall" is specified as true, then function will be
         executed after everything else. Only one function can be
         specified as lastCall.

         If nothing is being loaded, then functions are executed
         immediatelly
         */
        _onReady: function(fn,lastcall){
            if(lastcall){
                if(!this.loading){
                    fn.call(document);
                }else{
                    if(this._readyLast){
                        var prev=this._readyLast;
                        // call both functions if one is already there
                        this._readyLast=function(){
                            prev();
                            fn();
                        };
                    }else{
                        this._readyLast=fn;
                    }
                }
                return;
            }
            if(!this.loading){
                fn.call(document);
            }else{
                this._readyList.push(fn);
            }
        },
        /*
         if _onReady functions were not executed immediatelly, then
         this function will be called at the end and will execute them
         all in order. If any of the functions will start loading
         more files, execution will terminate and will be resumed
         after all files are loaded again.
         */
        _readyExec: function(){
            while(this._readyList.length){
                var fn=this._readyList.shift();
                fn.call(document);

                // We are loading more data, resume after
                if(jQuery.atk4.loading){
                    return;
                }
            }
            if(this._readyLast){
                var x=this._readyLast;
                this._readyLast=undefined;
                x.call(document);
            }
        },

        //////////////////// Dynamic Includes (CSS and JS) ////////////////

        /*
         Based on get() we add number of functions to dynamically load
         JS and CSS files.
         */


        // Lists of files we have already loaded. This is to ensure we do
        // not include JS and CSS files more than once
        _includes: {},

        // Loads javascript file and evals it
        includeJS: function(url,nocache){

            // Perhaps file is already included. We do not to load it twice
            if(this._isIncluded(url) && !nocache){
                return;
            }

            // Continue with loading
            this.get(url,function(code){
                //jQuery.globalEval(code);
                try{
                    eval(code);
                }catch(e){
                    // For non-production we better try to expose faulty code
                    // through browser JS parser
                    if(String(e).indexOf("Parse error")){
                        if(jQuery.atk4.production){
                            window.console.log("Eval failed for "+url);
                        }else{
                            window.console.error("Eval failed for "+url+", trying to include directly for debugging");
                            jQuery.atk4._evalJS(url);
                        }
                    }else {
                        window.console.error("Eval error: "+e);
                    }
                }
            });
        },
        // Use browser to natively include JS
        _evalJS: function(url,clean) {
            // remove previously evaled piece of code
            var old = document.getElementById('atk4_eval_clean');
            if (old !== null) {
                old.parentNode.removeChild(old);
            }
            var head = document.getElementsByTagName("head")[0];
            var script = document.createElement('script');
            if(clean){
                script.id = 'atk4_eval_clean';
            }
            script.type = 'text/javascript';
            script.src = url;
            head.appendChild(script);
        },
        /*
         This function will dynamically load CSS file.
         Also relative URLs like url(../images) will not break.
         */
        includeCSS: function(url){
            if(this._isIncluded(url)){
                return;
            }
            /*
             Dynamically loads CSS. Now works for IE too as noted in:
             http://stackoverflow.com/questions/1184950/dynamically-loading-css-stylesheet-doesnt-work-on-ie
             see comment by ekerner on Apr 4 11 at 16:44
             */
            jQuery("<link>")
                .appendTo(jQuery('head'))
                .attr({type : 'text/css', rel : 'stylesheet'})
                .attr('href', url);
        },
        _isIncluded: function(url){
            if(this._includes[url]){
                return true;
            }
            this._includes[url]=true;
            return false;
        },

        //////////////////////////// MISC //////////////////////////////////

        /*
         Utility function. When you give it an URL, and argument, it will
         append argument to the URL.

         TODO: this function is incomplete. It should also check if argument is
         already in the URL and handle that properly.

         See also: http://api.jquery.com/jQuery.param/
         */
        addArgument: function(url,a,b){
            if(typeof(url)=='object'){
                if(url[0]){
                    var u=url[0];
                    delete(url[0]);
                    jQuery.each(url,function(_a,_b){
                        u=jQuery.atk4.addArgument(u,_a,_b);
                    });
                    url=u;
                }
            }
            if(typeof(b)=='object'){
                console.log(b);
                var u=url;
                jQuery.each(b,function(_a,_b){
                    u=jQuery.atk4.addArgument(u,_a,_b);
                });
                return u;
            }
            if(typeof(a)=='undefined')return url;
            if(b)a+='='+encodeURIComponent(b);
            return url+(url.indexOf('?')==-1?'?':'&')+a;
        }

    });

})(jQuery);


// we use console.log a lot. It is handy in WebKit and Firebug, but
// would produce error in other browers. If method is not present,
// we define a blank one to avoid errors.
if(!window.console){
    window.region=jQuery;
    window.console={
        log: function(){
        },
        error: function(){
        }
    }
}

/*====================
 * ui-atk4-loader.js
 ====================*/

/* Welcome to Agile Toolkit JS framework. This is a main file which provides extensions on top of jQuery-UI */
/*
 @VERSION

 ATK4 Loader introduces a widget, which is used instead of default jQuery('..').load() method.

 The new widget adds a number of useful features including:
 * verifying loaded content against session timeouts or errors
 * provides better ability to refresh content
 * handler support. What if loaded content contains unsaved data?
 */

/*
 In a complex use case you would be doing the following:

 1. For the element which you wish to be a selector, initialise this widget
 2. Set a current URL for that element during initialisation (base_url);
 3. Define what should happen if user clicks "cancel"
 4. Define what should happen if user finishes action successfuly inside
 4a. succesful action may pass arguments.


 jQuery('#MyDiv')
 .atk4_loader({url: '/page.html'})
 .atk4_loader({})

 THE STRUCTURE OF THIS WIDGET MIGHT BE REWRITTEN

 */

jQuery.widget('ui.atk4_loader', {

    options: {
        /*
         base_url will contain URL which will be used to refresh contents.
         */
        base_url: undefined,

        /*
         loading will be set to true, when contents of this widgets are being
         loaded.
         */
        loading: false,
        cogs: '<div id="banner-loader" class="atk-banner atk-cells atk-visible"><div class="atk-cell atk-align-center atk-valign-middle"><div class="atk-box atk-inline atk-size-kilo atk-banner-cogs"></div></div></div>',

        /*
         when we are loading URLs, we will automaticaly pass arguments to cut stuff out
         */
        cut_mode: 'page',
        cut: '1',
        history: false
    },

    /*
     Helper contains some extra thingies
     */
    helper: undefined,
    loader: undefined,

    showLoader: function(){
        if(!!this.loader) this.loader.show();
    },
    hideLoader: function(){
        if(!!this.loader) this.loader.hide();
    },

    _create: function(){

        var self=this;
        /*
         this.options.debug=true;
         this.options.anchoring=true;
         */
        this.element.addClass('atk4_loader');

        if(this.options.url){
            this.base_url=this.options.url;
        }
        if(this.options.cut_object){
            this.cut_mode='object';
            this.cut=this.options.cut_object;
        }

        if(this.options.cogs){
            var l=jQuery(this.options.cogs);
            l.prependTo(self.element);
            self.loader=l;
            self.hideLoader();
        }

        if(this.options.history){
            jQuery(window).bind('popstate', function(event){
                var state = event.originalEvent.state;

                if (location.href != self.base_url && self.base_url) {
                    self.options.history=false;
                    self.loadURL(location.href,function(){
                        self.options.history=true;
                    });
                }
            });

        }

        if(this.options.debug){
            var d=jQuery('<div style="z-index: 2000"/>');
            d.css({background:'#fe8',border: '1px solid black',position:'absolute',width:'100px',height:'50px'});

            jQuery('<div/>').text('History: '+(this.options.history?'yes':'no')).appendTo(d);

            jQuery('<a/>').attr('title','Canceled close').attr('href','javascript: void(0)').text('X').css({float:'right'})
                .click(function(){ jQuery(this).closest('div').next().css({border:'0px'});jQuery(this).closest('div').remove(); }).appendTo(d);
            d.append(' ');
            jQuery('<a/>').attr('title','Reload this region').attr('href','javascript: void(0)').text('Reload')
                .click(function(){ self.reload()}).appendTo(d);
            d.append(' ');
            jQuery('<a/>').attr('title','Show URL').attr('href','javascript: void(0)').text('URL')
                .click(function(){ alert(self.base_url)}).appendTo(d);
            d.append(' ');
            jQuery('<a/>').attr('title','Attempt to remove').attr('href','javascript: void(0)').text('Remove')
                .click(function(){ self.remove()}).appendTo(d);
            d.append(' ');

            d.insertBefore(self.element);
            d.draggable();
            self.helper=d;
            self.element.css({border:'1px dashed green'});
        }
    },
    destroy: function(){
        var self=this;

        this.element.removeClass('atk4_loader');
        if(this.helper){
            this.helper.remove();
            this.helper=undefined;
        }
        if(this.loader){
            this.loader.remove();
            this.loader=undefined;
        }
    },


    /*
     This function fetches block of HTML from the server and puts it
     inside specified element. This function is very similar to
     jQuery('..').load('http://'), however it improves evaluation
     of scripts supplied inside loaded chunk.
     */
    _loadHTML: function(el, url, callback, reload){
        var self=this;

        // We preserve support for selectors in URL,
        // as compatibility with jQuery, however avoid
        // using this. ATK4 will gladly render part of
        // the page for you. (cut_object, cut_region, cut_page)
        var selector, off = url.indexOf(" ");
        if ( off >= 0 ) {
            selector = url.slice(off, url.length);
            url = url.slice(0, off);
        }

        // Before actual loading start, we call a method, which might want
        // to display loading indicator somewhere on the page.
        if(self.loading){
            jQuery.univ().loadingInProgress();
            return false;
        }
        var m;

        self.loading=true;
        self.showLoader();
        jQuery.atk4.get(url,null,function(res){
            /*
             if(res.substr(0,13)=='SESSION OVER:'){
             jQuery.univ.dialogOK('Session over','Your session have been timed out',function(){ document.location='/'});
             return;
             }
             */

            if(self.options.history)window.history.pushState({path: self.base_url}, 'foobar', self.base_url);

            var scripts=[], source=res;

            while((s=source.indexOf("<script"))>=0){
                s2=source.indexOf(">",s);
                e=source.indexOf("</script",s2);
                e2=source.indexOf(">",e);


                scripts.push(source.substring(s2+1,e));
                source=source.substring(0,s)+source.substring(e2+1);
            }

            m=el;
            //if(!(jQuery.browser.msie))m.hide();

            // Parse into Document
            var source=jQuery('<div/>').append(source);
            var n=source.children();

            var oldid=el.attr('id');
            if(n.length==1 && (reload || (n.attr('id') && n.attr('id')==oldid))){
                el.removeAttr('id');
                // Only one child have been returned to us. We also checked ID's and they match
                // with existing element. In this case we will be copying contents of
                // provided element
                //n=n.contents();
                el.triggerHandler('remove');
                n.insertAfter(el);
                el.remove();
                // http://forum.jquery.com/topic/jquery-empty-does-not-destroy-ui-widgets-whereas-jquery-remove-does-using-ui-1-8-4
            }else{
                // otherwise we will be copying all the elements (including text)
                if(reload){
                    console.error('Cannot reload content: ',reload,n[0],n[1],n[2]);
                }
                el.empty();
                n=source.contents();
                n.each(function(){
                    jQuery(this).remove().appendTo(el);
                });
            }

            el.atk4_loader({'base_url':url});

            /*
             */

            for(var i in scripts){
                try{
                    window.region=el;
                    if(eval.call)eval.call(window,scripts[i]);else
                    // IE-pain
                        with(window)eval(scripts[i]);
                }catch(e){
                    console.error("JS:",e,scripts[i]);
                }
            };

            if(callback)jQuery.atk4(callback,true);
            jQuery.atk4(function(){
                m.show();
                var f=m.find('form:first').find('input:visible,select:visible,textarea:visible').eq(0);
                if(!f.hasClass('nofocus'))f.focus();
            });
        },function(){	// second callback, which is always called, when loading is completed
            self.hideLoader();
            self.loading=false;
            el.trigger('after_html_loaded');
        });
    },
    /*
     This function is called before HTML loading is started. Redifine it
     or bind to enhance it's functionality
     */
    _loadingStart: function(){
        var self=this;
        if(false === self._trigger('loadingStart')){
            return false;
        }
    },

    reload: function(args){
        var url=this.base_url;
        if(args)this.base_url=jQuery.atk4.addArgument(this.base_url,args);
        this.loadURL(this.base_url);
        this.base_url=url;
    },
    remove: function(){
        var self=this;
        self.helper && self.helper.css({background:'red'});
        //if(false === self._trigger('beforeclose')){
        if(self.element.find('.form_changed').length){
            if(!confirm('Changes on the form will be lost. Continue?'))return false;
        }
        self.element.find('.form_changed').removeClass('form_changed');
        return true;
    },
    setURL: function(url){
        var self=this;

        self.base_url=url;
    },
    loadURL: function(url,fn,strip_layer){
        /*
         Function provided mainly for compatibility. It will load URL in the selector
         and will set "fn" to fire off when loading is complete

         Sometimes you would want to reload (atk4_reload) an element. This means,
         you will receive the same element from AJAX. New element comes with the
         same ID and sub-elements. What we have to do is copy children from the
         received data into existing element.
         */
        var self = this;

        if(self.loading){
            jQuery.univ().loadingInProgress();
            return false;
        }

        //if(false === self._trigger('beforeclose')){
        if(self.element.find('.form_changed').length){
            if(!confirm('Changes on the form will be lost. Continue?'))return false;
        }
        // remove error messages
        jQuery('#tiptip_holder').remove();
        self.base_url=url;
        url=jQuery.atk4.addArgument(url,"cut_"+self.options.cut_mode+'='+self.options.cut);
        this._loadHTML(self.element,url,fn,strip_layer);
    }

});

jQuery.extend(jQuery.ui.atk4_loader, {
    getter: 'remove'
});

jQuery.fn.extend({
    atk4_load: function(url,fn){
        this.atk4_loader().atk4_loader('loadURL',url,fn);
    },
    atk4_reload: function(url,arg,fn){
        if(arg){
            jQuery.each(arg,function(key,value){
                url=jQuery.atk4.addArgument(url,key+'='+encodeURIComponent(value));
            });
        }
        this.atk4_loader()
            .atk4_loader('loadURL',url,fn,true);
    }
});

/*====================
 * ui.atk4_notify.js
 ====================*/

/**
 * ATK Notification UI Widget
 */

(function(jQuery){
    jQuery.widget('ui.atk4_notify', {

        /**
         * Configuration
         */
        options: {

            // Set function here how to show/hide a message
            show: function(){ this.fadeIn() },
            hide: function(){ this.fadeOut() },

            // Timeout in miliseconds
            min_timeout: 3000,
            inc_timeout: 25,

            // Close button
            closable: true,
            close_text: 'Hide this message'
        },

        /**
         * Display success message
         *
         * @param string text Message text to show
         */
        successMessage: function(text) {
            this.message(text, true);
        },

        /**
         * Display error message
         *
         * @param string text Message text to show
         */
        errorMessage: function(text) {
            this.message(text, false);
        },

        /**
         * Display message
         *
         * @param string text Message text to show
         * @param boolean success Show success message if true, error message otherwise
         */
        message: function(text, success) {
            var html = jQuery(
                '<div class="atk-notification ui-state-'+(success?'highlight':'error')+' ui-corner-all">'+
                '<div class="atk-notification-text">'+
                '<i class="ui-icon ui-icon-'+(success?'info':'alert')+'"></i>'+
                '<span>'+text+'</span>'+
                '</div>'+
                (this.options.closable
                        ? '<i title="'+this.options.close_text+'" class="ui-icon ui-icon-closethick"></i>'
                        : ''
                ) +
                '</div>');

            this.messageHTML(html);
        },

        /**
         * Prepare message object and show it
         *
         * @param jQuery message Message to show
         */
        messageHTML: function(message) {
            this._defineBehaviour(message);
            this._customiseMessage(message);
            this._insertMessage(message);
        },

        /**
         * Define behaviour of message object
         *
         * @param jQuery message Message object
         */
        _defineBehaviour: function(message) {
            var self = this;

            // When close button is present - use it
            if (this.options.closable) {
                message.find('.ui-icon-closethick').click(function() {
                    message.stop();
                    self.options.hide.call(message);
                });
            } else {
                // otherwise, the whole message disappears on click
                message.click(function() {
                    message.stop();
                    self.options.hide.call(message);
                });
            }
        },

        /**
         * Redefine this to add some custom markup or actions for your messages
         *
         * @param jQuery message Message object
         */
        _customiseMessage: function(message) {
            // message.addClass('light-gray');
        },

        /**
         * Add message object into container
         *
         * @param jQuery message Message object
         */
        _insertMessage: function(message) {
            message.hide();
            this.element.prepend(message);

            this.options.show.call(message);
            message.delay(this._getTimeout(message));
            this.options.hide.call(message);

            message.hide(0, function() {
                message.remove();
            });
        },

        /**
         * Return time in ms for how long we should show message.
         *
         * @param jQuery message Message object
         */
        _getTimeout: function(message) {
            return this.options.min_timeout +
                message.text().length * this.options.inc_timeout;
        }
    });

})(jQuery);

/*====================
 * atk4_univ_basic.js
 ====================*/

/* Welcome to Agile Toolkit JS framework. This file provides universal chain. */

// jQuery allows you to manipulate element by doing chaining. Similarly univ provides
// loads of simple functions to perform action chaining.
//

jQuery||console.error("jQuery must be loaded");
(function(jQuery){


    jQuery.each({
        alert: function(a){
            alert(a);
        },
        displayAlert: function(a){
            alert(a);
        },
        /*
         // Upgraded changed interval and timeout solution like adviced here:
         // https://developer.mozilla.org/en/docs/Web/API/window.setInterval#A_possible_solution
         setTimeout: function(code,delay){
         setTimeout(code,delay);
         },
         setInterval: function(code,delay){
         return setInterval(code,delay);
         },
         */
        setTimeout: function(callback, delay /*, arg1, arg2, ... */){
            var oThis = this.jquery;
            var aArgs = Array.prototype.slice.call(arguments, 2);
            return window.setTimeout(
                callback instanceof Function
                    ? function() {callback.apply(oThis, aArgs);}
                    : callback
                , delay);
        },
        setInterval: function(callback, delay /*, arg1, arg2, ... */){
            var oThis = this.jquery;
            var aArgs = Array.prototype.slice.call(arguments, 2);
            return window.setInterval(
                callback instanceof Function
                    ? function() {callback.apply(oThis, aArgs);}
                    : callback
                , delay);
        },
        clearTimeout: function(id){
            window.clearTimeout(id);
        },
        clearInterval: function(id){
            window.clearInterval(id);
        },
        redirect: function(url,fn){
            if(jQuery.fn.atk4_load && jQuery('#Content').hasClass('atk4_loader')){
                jQuery.univ.page(url,fn);
            }else{
                jQuery.univ.location(url);
            }
        },
        redirectURL: function(url,fn){
            jQuery.univ.redirect(url,fn);
        },
        location: function(url){
            url=jQuery.atk4.addArgument(url);
            if(!url)document.location.reload(true);else
                document.location=url;
        },
        page: function(page,fn){
            jQueryc=jQuery('#Content');
            if(!jQueryc.length)jQueryc=this.jquery;
            jQueryc.atk4_load(page,fn);
        },
        log: function(arg1){
            if(console)console.log(arg1);
        },
        consoleError: function(arg1){
            if(console){
                if(console.error)console.error(arg1);
                else console.log('Error: '+arg1);
            }
        },
        confirm: function(msg){
            if(!msg)msg='Are you sure?';
            if(!confirm(msg))this.ignore=true;
        },
        displayFormError: function(form,field,message){
            console.log(form,field,message);
            if(!message){
                message=field;
                field=form;
            }
            if(form){
                var el=jQuery(form);
                // TODO - pass on action to form widget

            }
            this.alert(field+": "+message);
        },
        setFormFocus: function(form,field){
            jQuery('#'+form+' input[name='+form+'_'+field).focus();
        },
        closeExpander: function(){
            var e=this.jquery.closest('.lister_expander').parent().prev().find('.expander');
            if(!e.length)e=jQuery('.expander');

            e.atk4_expander('collapse');
        },
        closeExpanderWidget: function(){
            this.closeExpander();
        },
        reloadExpandedRow: function(id){
            if(!id)id=this.jquery.closest('.lister_expander').parent().prev().attr('rel');
            this.closeExpander();
            var g=this.jquery.closest('.atk4_grid');
            g.atk4_grid('reloadRow',id);
        },
        removeExpandedRow: function(id){
            if(!id)id=this.jquery.closest('.lister_expander').parent().prev().attr('rel');
            this.closeExpander();
            var g=this.jquery.closest('.atk4_grid');
            g.atk4_grid('removeRow',id);
        },
        loadRegionUrlEx: function(id,url){
            jQuery('#'+id).load(url);
        },
        memorizeExpander: function(){ },
        submitForm: function(form,button,spinner){
            var successHandler=function(response_text){
                if(response_text){
                    try {
                        eval(response_text);
                    }catch(e){
                        //while some browsers prevents popup we better use alert
                        w=window.open(null,null,'height=400,width=700,location=no,menubar=no,scrollbars=yes,status=no,titlebar=no,toolbar=no');
                        if(w){
                            w.document.write('<h2>Error in AJAX response: '+e+'</h2>');
                            w.document.write(response_text);
                            w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
                        }else{
                            console.log(response_text, e);
                            showMessage("Error in AJAX response: "+e+"\n"+response_text);
                        }
                        try{
                            eval(response_text.substring(response_text.indexOf('//ajax_script_start'),response_text.lastIndexOf('//ajax_script_end')));
                        } catch(e) {
                            if(w){
                                w.document.write('<h2>Error in AJAX response: '+e+'</h2>');
                                w.document.write(response_text);
                                w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
                            }else{
                                showMessage('Could not parse response. '+e);
                            }
                        }
                    }
                } else {
                    showMessage("Warning: Empty response from server");
                }
                if(spinner)spinner_off(spinner);
            };
            // adding hidden field with clicked button value
            if(button){
                btn_value=button.substring(button.lastIndexOf('_')+1);
                button=jQuery('<input name="'+button+'" id="'+button+'" value="'+btn_value+'" type="hidden">');
                jQuery('#'+form).append(button);
            }
            // adding a flag for ajax submit
            jQuery(form).append(jQuery('<input name="ajax_submit" id="ajax_submit" value="1" type="hidden">'));
            jQuery(form).ajaxSubmit({success: successHandler});
            // removing hidden field
            if(button)button.remove();
        },
        addArgument: function(url,args){
            return jQuery.atk4.addArgument(url,args);
        },
        reloadArgs: function(url,key,value){
            var u=jQuery.atk4.addArgument(url,key+'='+value);
            this.jquery.atk4_load(u);
        },
        reload: function(url,arg,fn,interval){
            /*
             * jQueryobj->js()->reload();	 will now properly reload most of the objects.
             * This function can be also called on a low level, however URL have to
             * be specified.
             * jQuery('#obj').univ().reload('http://..');
             *
             * Difference between atk4_load and this function is that this function
             * will correctly replace element and insert it into container when
             * reloading. It is more suitable for reloading existing elements.
             *
             * If interval is set, then object will be periodically reloaded.
             */

            // if interval is set, then do periodic reloads / refreshes
            // otherwise just reload object one time
            if(interval) {
                // execute reload after defined period of time
                var id = this.setTimeout(function(){
                    this.atk4_reload(url,arg,fn);
                }, interval);
                // if associated DOM element is destroyed, then remove timeout action
                this.jquery.on('remove',function(ev){
                    jQuery.univ.clearTimeout(id);
                });
            } else {
                this.jquery.atk4_reload(url,arg,fn);
            }
        },
        reloadParent: function(depth,args){
            if(!depth)depth=1;
            var atk=this.jquery;
            var patk=atk;
            while(depth-->0){
                atk=atk.closest('.atk4_loader');
                if(atk.length)patk=atk;
                if(!depth){
                    if(atk.length)atk.atk4_loader('reload',args);else
                        patk.atk4_loader('reload',args);
                }else{
                    atk=atk.parent();
                }
            }
        },
        reloadContents: function(url,arg,fn){
            /*
             * Identical to reload(), but instead of reloading element itself,
             * it will reload only contents of the element.
             */

            if(arg){
                jQuery.each(arg,function(key,value){
                    url=jQuery.atk4.addArgument(url,key+'='+value);
                });
            }

            this.jquery.atk4_load(url,fn);
        },
        saveSelected: function(name,url){
            result=new Array();
            i=0;
            jQuery('#'+name+' input[type=checkbox]').each(function(){
                result[i]=jQuery(this).attr('value')+':'+(jQuery(this).attr('checked')==true?'Y':'N');
                i++;
            });
            jQuery.get(url+'&selected='+result.join(','),null,function(res){
                try {
                    eval(res);
                }catch(e){
                    //while some browsers prevents popup we better use alert
                    w=window.open(null,null,'height=400,width=700,location=no,menubar=no,scrollbars=yes,status=no,titlebar=no,toolbar=no');
                    if(w){
                        w.document.write('<h2>Error in AJAX response: '+e+'</h2>');
                        w.document.write(res);
                        w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
                    }else{
                        showMessage("Error in AJAX response: "+e+"\n"+response_text);
                    }
                }
            });
        },
        executeUrl: function(url,callback){
            jQuery.get(url,callback);
        },
        ajaxFunc:	function(str_code){
            jQuery.globalEval(str_code);
        },
        reloadRow:	function(id){
            // Reload row of active grid
            var grid=this.jquery.closest('.atk4_grid');
            grid.atk4_grid('reloadRow',id);
        },
        removeRow:	function(id){
            // Reload row of active grid
            var grid=this.jquery.closest('.atk4_grid');
            grid.atk4_grid('removeRow',id);
        },
        removeOverlay: function(){
            var grid=this.jquery.closest('.atk4_grid');
            if(!grid.length){
                console.log('removeOverlay cannot find grid');
            }
            grid.atk4_grid('removeOverlay');
        },
        fillFormFromFrame: function(options){
            /*
             * Use this function from inside frame to insert values into the form which originally opened it
             */
            var j=this.jquery;
            var form=this.getFrameOpener();
            form=form.closest('form');
            var form_id=form.attr('id');

            jQuery.each(options, function(key,value){
                form.atk4_form('setFieldValue',key,value);
            });
            this.jquery=j;

        },
        getjQuery: function(){
            return this.jquery;
        },
        ajaxec: function(url,data,fn){
            // Combination of ajax and exec. Will pull provided url and execute returned javascript.
            region=this.jquery;

            if(region.data('ajaxec_loading'))return this.successMessage('Please Wait');
            region.data('ajaxec_loading',true);


            if(data==true){
                data={};
                if(region.data()) {
                    jQuery.each(region.data(), function(k, v) {
                        if(typeof v !== "object") data[k]=v;
                    });
                }

            }

            var cogs=jQuery('<div id="banner-loader" class="atk-banner atk-cells atk-visible"><div class="atk-cell atk-align-center atk-valign-middle"><div class="atk-box atk-inline atk-size-kilo atk-banner-cogs"></div></div></div>');
            cogs.appendTo('body');

            jQuery.atk4.get(url,data,function(ret){
                cogs.remove();
                region.data('ajaxec_loading',false);
                /*
                 // error handling goes away from here
                 if(ret.substr(0,5)=='ERROR'){
                 jQuery.univ().dialogOK('Error','There was error with your request. System maintainers have been notified.');
                 return;
                 }
                 */
                if(!jQuery.atk4._checkSession(ret))return;
                try{
                    eval(ret);
                    if(fn)fn();
                }catch(e){
                    w=window.open(null,null,'height=400,width=700,location=no,menubar=no,scrollbars=yes,status=no,titlebar=no,toolbar=no');
                    if(w){
                        w.document.write('<h5>Error in AJAXec response: '+e+'</h5>');
                        w.document.write(ret);
                        w.document.write('<center><input type=button onclick="window.close()" value="Close"></center>');
                    }else{
                        console.log("Error in ajaxec response", e,ret);
                        showMessage("Error in AJAXec response: "+e+"\n"+ret);
                    }
                }

            },null,true);
        },
        newWindow: function(url,name,options){
            window.open(url,name,options);
        },
        expr: function(str){
            return eval("(" + str + ")");
        },
        ajaxifyLinks: function(){
            // Links of the current container will be opened in the closest loader
            this.jquery.find('td a').click(function(ev){
                ev.preventDefault();
                jQuery(this).closest('.atk4_loader').atk4_loader('loadURL',jQuery(this).attr('href'));
            });
        },
        autoChange: function(interval){
            // Normally onchange gets triggered only when field is submitted. However this function
            // will make field call on_change one second since last key is pressed. This makes event
            // triggering more user-friendly
            var f=this.jquery;
            var f0=f.get(0);
            if(typeof interval == 'undefined')interval=1000;

            f.attr('data-val',f.val());

            function onkeyup(){
                if(f.attr('data-val')==f.val())return;
                f.attr('data-val',f.val());
                var timer=jQuery.data(f0,'timer');
                if(timer){
                    clearTimeout(timer);
                }
                if(interval){
                    timer=setTimeout(function(){
                        jQuery.data(f0,'timer',null);
                        f.trigger('autochange');
                        f.change();
                    },interval);
                    jQuery.data(f0,'timer',timer);
                }else{
                    f.trigger('autochange');
                    f.change();
                }
            }
            //f.change(onchange);
            f.keyup(onkeyup);
            f.bind('autochange_manual',onkeyup);
        },
        numericField: function(){
            this.jquery.bind('keyup change',function () {
                var t= this.value.replace(/[^0-9\.-]/g,'');
                if(t != this.value)this.value=t;
            });
        },
        onKey: function(code,fx,modifier,modival){
            this.jquery.bind('keydown',function(e){
                if(e.which==code && (!modifier || e[modifier]==modival)){
                    e.preventDefault();
                    e.stopPropagation();
                    return fx();
                }
            });
        },
        disableEnter: function(){
            this.jquery.bind('keydown keypress',function (e) {
                if(e.which==13){
                    return false;
                }
            });
        },
        bindConditionalShow: function(conditions,tag){
            // Warning
            //   this function does not handle recursive cases,
            //   when element A hides element B which should also hide
            //   element C. You may end up with B hidden and C still showing.
            var f=this.jquery;
            var n=f.closest('form').parent();
            if(!n.attr('id'))n=n.parent();
            n=n.attr('id');

            if(typeof tag == 'undefined')tag='div.atk-row';

            var sel=function(name){
                var s=[];
                fid=n;
                jQuery.each(name,function(){
                    var a = (this[0]=='#'?this+'':"[data-shortname='"+this+"']");
                    var dom = jQuery(a, '#'+fid)[0];
                    if(dom){
                        s.push(dom);
                    }else{
                        console.log("Field is not defined",a);
                    }
                });
                s=jQuery(s);
                if(tag){
                    s=s.closest(tag);
                }
                return s;
            }

            var ch=function(){
                if(f.is('.atk-checkboxlist,.atk-form-options')){
                    var v=[];
                    f.find('input:checked').each(function(){
                        v.push(this.value);
                    });
                }else{
                    var v=f.val();
                }
                if(f.is(':checkbox'))v=f[0].checked?v:'';
                if(f.is('select')){
                    v=f.find('option:selected').val();
                }

                // first, lets hide everything we can
                jQuery.each(conditions,function(k,x){
                    s=sel(this);
                    if(s.length){
                        s.hide();
                    }
                });

                // Next, let's see if there is an exact match for that
                var exact_match=null;
                if(v instanceof Array){
                    exact_match=[];
                    jQuery.each(v,function(k,val){
                        if(typeof conditions[val] != 'undefined'){
                            exact_match.push(sel(conditions[val]));
                        }
                    });
                    if(!exact_match.length && typeof conditions['*'] != 'undefined'){
                        exact_match=sel(conditions['*']);
                    }
                }else{
                    if(typeof conditions[v] != 'undefined'){
                        exact_match=sel(conditions[v]);
                    }else if(typeof conditions['*'] != 'undefined'){
                        // catch-all value exists
                        exact_match=sel(conditions['*']);
                    }
                }

                //console.log(exact_match);

                if(exact_match && exact_match.length){
                    if(exact_match instanceof Array){
                        jQuery.each(exact_match,function(k,val){
                            val.show();
                        });
                    }else{
                        exact_match.show();
                    }
                }
            }
            if(f.hasClass('field_reference')){
                f.bind('change_ref',ch);
            }else if(f.hasClass('atk-checkboxlist')){
                f.find('input[type=checkbox]').bind('change',ch);
            }else{
                f.change(ch);
            }
            ch();
        },

        bindFillInFields: function(fields){
            /*
             * This is universal function for autocomplete / dropdown fields. Whenever original field changes,
             *  we will use information in "rel" attribute of orignial field to fill other fields
             *  with appropriate values
             */
            var f=this.jquery;


            jQuery.each(fields,function(key,val){
                jQuery(val).change(function(){ jQuery(this).addClass('manually_changed'); });
            });


            function onchange_fn(){
                var data=eval('('+f.attr('rel')+')');
                var myid=jQuery(this).val();
                data=data[myid];

                function auto_fill(){
                    jQuery.each(fields,function(key,val){
                        if(data && data[key]){
                            jQuery(val).val(data[key]).change().removeClass('manually_changed');
                        }
                    });
                };

                // Make sure none of those fields were changed manually.
                var need_to_warn = false;
                jQuery.each(fields,function(key,val){
                    if(data && data[key] && jQuery(val).hasClass('manually_changed') && jQuery(val).val()){
                        need_to_warn=true;
                    }
                });

                if(need_to_warn)
                    jQuery(this).univ().dialogConfirm('Warning','Some fields you have edited are about to be auto filled. Would you like to proceed?',auto_fill);
                else
                    auto_fill();
            };

            f.bind('change_ref change',onchange_fn);
        }
    },jQuery.univ._import);

    jQuery.extend(jQuery.univ,{

        // Function with custom return value

        toJSON: function (value, whitelist) {
            var m = {
                '\b': '\\b',
                '\t': '\\t',
                '\n': '\\n',
                '\f': '\\f',
                '\r': '\\r',
                '"' : '\\"',
                '\\': '\\\\'
            };

            var a,          // The array holding the partial texts.
                i,          // The loop counter.
                k,          // The member key.
                l,          // Length.
                r = /["\\\x00-\x1f\x7f-\x9f]/g,
                v;          // The member value.
            switch (typeof value) {
                case 'string':
                    return r.test(value) ?
                    '"' + value.replace(r, function (a) {
                        var c = m[a];
                        if (c) {
                            return c;
                        }
                        c = a.charCodeAt();
                        return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
                    }) + '"' :
                    '"' + value + '"';
                case 'number':
                    return isFinite(value) ? String(value) : 'null';
                case 'boolean':
                case 'null':
                    return String(value);
                case 'object':
                    if (!value) {
                        return 'null';
                    }
                    if (typeof value.toJSON === 'function') {
                        return this.toJSON(value.toJSON());
                    }
                    a = [];
                    if (typeof value.length === 'number' &&
                        !(value.propertyIsEnumerable('length'))) {
                        l = value.length;
                        for (i = 0; i < l; i += 1) {
                            a.push(this.toJSON(value[i], whitelist) || 'null');
                        }
                        return '[' + a.join(',') + ']';
                    }
                    if (whitelist) {
                        l = whitelist.length;
                        for (i = 0; i < l; i += 1) {
                            k = whitelist[i];
                            if (typeof k === 'string') {
                                v = this.toJSON(value[k], whitelist);
                                if (v) {
                                    a.push(this.toJSON(k) + ':' + v);
                                }
                            }
                        }
                    } else {
                        for (k in value) {
                            if (typeof k === 'string') {
                                v = this.toJSON(value[k], whitelist);
                                if (v) {
                                    a.push(this.toJSON(k) + ':' + v);
                                }
                            }
                        }
                    }
                    return '{' + a.join(',') + '}';
            }
        }
    });

// Fix annoying behaviour of dialog, where it removes itself from
// parent

////// Define deprecated functions ////////////
    jQuery.each([
        'openExpander'
    ],function(name,val){
        jQuery.univ[val]=function(){
            console.error('Function is deprecated:',val);
            return jQuery.univ;
        }
    });

})(jQuery);

/*====================
 * atk4_univ_jui.js
 ====================*/

jQuery.each({
    dialogPrepare: function(options){
        /*
         * This function creates a new dialog and makes sure other dialog-related functions will
         * work perfectly with it
         */
        var dialog=jQuery('<div class="dialog dialog_autosize" title="Untitled"><div style="min-height: 300px"></div></div>').appendTo('body');
        if(options.noAutoSizeHack)dialog.removeClass('dialog_autosize');
        dialog.dialog(options);
        if(options.customClass){
            dialog.parent().addClass(options.customClass);
        }
        jQuery.data(dialog.get(0),'opener',this.jquery);
        jQuery.data(dialog.get(0),'options',options);
        jQuery(window).resize(function() {
            dialog.dialog("option", "position", {my: "center", at: "center", of: window});
        });

        return dialog;
    },
    getDialogData: function(key){
        var dlg=this.jquery.closest('.dialog').get(0);
        if(!dlg)return null;
        var r=jQuery.data(dlg,key);
        if(!r){
            return null;
        }
        return r;
    },
    getFrameOpener: function(){
        var d=this.getDialogData('opener');
        if(!d)return null;
        return jQuery(this.getDialogData('opener'));
    },
    dialogBox: function(options){

        if (!options.ok_label) options.ok_label = 'Ok';
        if (!options.ok_class) options.ok_class = 'atk-effect-primary';

        var buttons=[];

        buttons.push({
            text: options.ok_label,
            class: options.ok_class,
            click: function(){
                var f=jQuery(this).find('form');
                if(f.length)f.eq(0).submit(); else jQuery(this).dialog('close');
            }
        });
        buttons.push({
            text: 'Cancel',
            click: function(){
                jQuery(this).dialog('close');
            }
        });

        return this.dialogPrepare(jQuery.extend({
            bgiframe: true,
            modal: true,
            width: 1000,
            position: { my:'top',at:'top+100','of':window },
            autoOpen:false,
            beforeClose: function(){
                if(jQuery(this).is('.atk4_loader')){
                    if(!jQuery(this).atk4_loader('remove'))return false;
                }
            },
            buttons: buttons,
            open: function(x){
                jQuery("body").css({ overflow: 'hidden' })
                    .children('.atk-layout').addClass('visible-dialog');
                jQuery(x.target).css({'max-height': jQuery(window).height()-180});
            },
            close: function(){
                jQuery("body").css({ overflow: 'auto' })
                    .children('.atk-layout').removeClass('visible-dialog');
                jQuery(this).dialog('destroy');
                jQuery(this).remove();
            }
        },options));
    },
    dialogURL: function(title,url,options,callback){
        if(typeof url == 'undefined'){
            url=title;
            title='Untitled Dialog';
        }
        var dlg=this.dialogBox(jQuery.extend(options,{title: title,autoOpen: true}));
        dlg.closest('.ui-dialog').hide().fadeIn('slow');
        dlg.atk4_load(url,callback);
        return dlg.dialog('open');
    },
    frameURL: function(title,url,options,callback){
        options=jQuery.extend({
            buttons:{}
        },options);
        return this.dialogURL(title,url,options,callback);
    },
    dialogOK: function(title,text,fn,options){
        var dlg=this.dialogBox(jQuery.extend({
            title: title,
            width: 450,
            //height: 150,
            close: fn,
            open: function() {
                jQuery(this).parents('.ui-dialog-buttonpane button:eq(0)').focus();
            },
            buttons: {
                'Ok': function(){
                    jQuery(this).dialog('close');
                }
            }
        },options));
        dlg.html(text);
        dlg.dialog('open');

    },
    dialogConfirm: function(title,text,fn,options){
        /*
         * Displays confirmation dialogue.
         */
        var dlg=this.dialogBox(jQuery.extend({title: title, width: 450, height: 200},options));

        dlg.html("<form></form>"+text);
        dlg.find('form').submit(function(ev){ ev.preventDefault(); if(fn)fn(); dlg.dialog('close'); });
        dlg.dialog('open');
    },
    dialogError: function(text,options,fn){
        this.dialogConfirm('Error','<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text,null,
            jQuery.extend({buttons:{'Ok':function(){ jQuery(this).dialog('close');if(fn)fn()}}},options));
    },
    dialogAttention: function(text,options,fn){
        this.dialogConfirm('Attention!','<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text,null,
            jQuery.extend({buttons:{'Ok':function(){ jQuery(this).dialog('close');if(fn)fn()}}},options));
    },
    message: function(msg,html){
        html.find('span').text(msg);

        html.find('.do-close').click(function(e){e.preventDefault();html.remove();});

        var dest=jQuery("body");
        if(dest.length){
            html.prependTo(dest);
            return html;
        }else{
            alert(msg);
            return false;
        }
    },
    successMessage: function(msg,time){
        var html=jQuery('<div class="atk-layout-row" style="position: fixed; z-index: 1000">\
    <div class="atk-swatch-green atk-cells atk-padding-small">\
      <div class="atk-cell atk-jackscrew"><i class="icon-info"></i>&nbsp;<span>Agile Toolkit failed to automatically renew certificate.</span></div>\
      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
    </div>\
  </div>');
        this.message(msg,html);
        setTimeout(function() { html.remove();},time?time:8000);
    },
    errorMessage: function(msg,time){
        var html=jQuery('<div class="atk-layout-row" style="position: fixed; z-index: 1000">\
    <div class="atk-swatch-red atk-cells atk-padding-small">\
      <div class="atk-cell atk-jackscrew"><i class="icon-attention"></i>&nbsp;<span>Agile Toolkit failed to automatically renew certificate.</span></div>\
      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>\
    </div>\
  </div>');
        this.message(msg,html);
        if(time)setTimeout(function() { html.remove();},time);
    },
    closeDialog: function(){
        var r=this.getFrameOpener();
        if(!r)return;
        this.jquery.closest('.dialog').dialog('close');
        this.jquery=r;
    },
    loadingInProgress: function(){
        this.successMessage('Loading is in progress. Please wait');
    }
},jQuery.univ._import);

var oldcr = jQuery.ui.dialog.prototype._create;
jQuery.ui.dialog.prototype._create = function(){
    var self=this;
    jQuery('<div/>').insertBefore(this.element).on('remove',function(){
        self.element.remove();
    });
    oldcr.apply(this,arguments);
};



/**
 * _allowInteraction fix to accommodate windowed editors
 *
 * This is blocker issue if you want to open CKEditor or TinyMCE editor dialog
 * from JUI dialog because JUI doesn't give focus outside of it's dialog window.

 * @url http://bugs.jqueryui.com/ticket/9087#comment:39
 * @url https://learn.jquery.com/jquery-ui/widget-factory/extending-widgets/#using-_super-and-_superapply-to-access-parents
 * @note Tested on jQuery UI v1.11.x
 */
jQuery.widget( "ui.dialog", jQuery.ui.dialog, {
    _allowInteraction: function( event ) {
        if ( this._super( event ) ) {
            return true;
        }

        // address interaction issues with general iframes with the dialog
        if ( event.target.ownerDocument != this.document[ 0 ] ) {
            return true;
        }

        // address interaction issues with dialog window
        if ( !!jQuery( event.target ).closest( ".cke_dialog, .mce-window, .moxman-window" ).length ) {
            return true;
        }

        // address interaction issues with iframe based drop downs in IE
        if ( !!jQuery( event.target ).closest( ".cke" ).length ) {
            return true;
        }
    }
});

/*====================
 * wp-atk4_univ_ext.js
 ====================*/

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
jQuery.each({
    message: function(msg,html){
        html.find('span').text(msg);

        html.find('.do-close').click(function(e){e.preventDefault();html.remove();});

        var dest=jQuery(".atk-wp-body");
        if(dest.length){
            html.prependTo(dest);
            return html;
        }else{
            alert(msg);
            return false;
        }
    },
    atkWpMessage: function(id, effect, msg, time){
        var html, dest;

        if(time == undefined){
            time = 4000;
        }


        getDestination = function(){
            var container = jQuery('#' + id).parents('.atk-wp-body').find('.atkwp-notice');
            if ( ! container.length ) {
                //check for a shortcode output
                container = jQuery('#' + id).find('.atkwp-notice');
            }
            return container;
        };

        html = jQuery('<div class="atk-layout-row" style="position: absolute; z-index: 1000;"></div>')
            .append( jQuery('<div class="atk-effect-' + effect + ' atk-cells atk-box-small"></div>')
                .append( jQuery('<div class="atk-cell atk-jackscrew"></div>')
                    .append( jQuery('<i class="icon-info"></i>'))
                    .append( jQuery('<span>' + msg + '</span>')))
                .append( jQuery('<div class="atk-cell"></div>')
                    .append( jQuery('<a href="javascript: void()" ></a>'))
                    .click(function(e){e.preventDefault();html.remove();})
                    .append( jQuery('<i class="icon-cancel"></i>'))
                )
            );

        if( dest = getDestination() ){
            html.prependTo(dest);
        }else{
            alert(msg);
        }
        setTimeout(function() { html.remove();},time);
    }
},jQuery.univ._import);
