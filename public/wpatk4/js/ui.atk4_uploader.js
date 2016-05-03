/* Welcome to Agile Toolkit JS framework. This file implements Uploader. */

// The following HTML structure should be used:
//
// <input type=file>     <!-- binding to this element -->
//
jQuery.widget("ui.atk4_uploader", {
    options: {
		'flash': false,
		'iframe': false,
		'multiple': 1,
    },
	shown: true,

	_setChanged: function(){
		this.element.closest('form').addClass('form_changed');
	},


    _create: function(){
		var self=this;
		if(!this.options.form)this.options.form="#"+closest('form').parent().attr('id');
		this.name=this.element.attr('id');

		if(this.options.flash){
			this.initSWF();
		}

		if(this.options.iframe){
			this.element.change(function(){
				self.upload();
			})
		}


	},

	// we supply different upload techniques, and can change later based on browser support.
	initSWF: function(){
		var uploader=this;
		uploader.element.hide();	// do not show while loading..
		uploader.element.after('<input type="hidden" id="'+uploader.name+'_token">');

		jQuery.atk4.includeCSS('/amodules3/templates/js/uploadify/uploadify.css');
		jQuery.atk4.includeJS('/amodules3/templates/js/uploadify/swfobject.js');
		jQuery.atk4.includeJS('/amodules3/templates/js/uploadify/jquery.uploadify.v2.1.0.min.js');

		jQuery.atk4(function(){
			uploader.element.uploadify(i={
				'uploader':'/amodules3/templates/js/uploadify/uploadify.swf',
				'script': '/upload/',
				'scriptAccess': 'always',
				'buttonText':'Upload new',
				'auto': true,
				'fileDataName': 'Default',
				'sizeLimit': uploader.options.size_limit,
				'onComplete': function(){ return uploader.completeSWF.apply(uploader,arguments)},
				'cancelImg': '/amodules3/templates/js/uploadify/cancel.png'
			});
			console.log(i);
		});
		

	},
	upload: function(){
		var form_wrapper=jQuery(this.options.form);
		var form=form_wrapper.find('form');
		var oa=form.attr('action');

		// add dynamically if it's missing
		var i=jQuery('<div style="display: inline"/>');
		i[0].innerHTML='<iframe id="'+this.name+'_iframe" name="'+this.name+'_iframe" src="about:blank" style="width:0;height:0;border:0px solid black;"></iframe>';
		i.insertBefore(this.element);

		var g=jQuery('<div class="atk-loader" id="'+this.name+'_progress"><i></i>Uploading '+this.element.val()+'</div>')
			.insertBefore(this.element);

		form
			.attr('action',oa+'&'+this.element.attr('name')+'_upload_action='+this.name)
			.attr('target',this.name+"_iframe");

		form_wrapper.atk4_form('submitPlain');

		form
			.removeAttr('target')
			.attr('action',oa);

		// fool-proof way to clone element. Firefox will copy seelcted file, while safari will not
		var el=this.element.clone().attr('id',this.name+'_');

		// Silly firefox - copies uploaded file value
		el=el.wrap('<div/>').parent();
		el[0].innerHTML=el[0].innerHTML;
		el=el.find('input');

		el.insertAfter(this.element).atk4_uploader(this.options);

		var files=jQuery("#"+this.element.attr('name')+"_files").find('.files-container').children('div').not('.template').length;
		if(files+1>=this.options.multiple)el.hide(); //does this work actually? I mean the el.hide()
		this.element.hide();
	},
	addFiles: function(data){
		// Uses template to populate rows in the table
		var tb=jQuery("#"+this.element.attr('name')+"_files").find('.files-container');
		var self=this;
		var act=this.element.closest('form').attr('action');

		jQuery.each(data,function(i,row){
			var tpl=tb.find('.template')
				.clone()
				.attr('rel',row['id'])// <--easier to debug
				.attr('data-url',row['url'])// <--easier to debug
				.removeClass('template')
				.show();
			jQuery.each(row,function(key,val){
				tpl.find('[data-template='+key+']').text(val);
			});
			tpl.find('.delete_doc').click(function(ev){
                var tmp = this;
                jQuery(this).univ().dialogConfirm('Confirmation required', 'Do you want to delete this file?', function(){
                    ev.preventDefault();
                    jQuery(tmp).univ().ajaxec(act+'&'+
                        self.element.attr('name')+'_delete_action='+
                        jQuery(tmp).closest('div').attr('rel')
                    );
                });
			})
			tpl.find('.add_image').click(function(ev){
				ev.preventDefault();
				var url=act+'&view=true&'+self.element.attr('name')+'_save_action='+ jQuery(this).closest('div').attr('rel');
				jQuery('.atk4_richtext').atk4_richtext('append','<img src="'+url+'"/>');
			})
			tpl.find('.add_image_elrte').click(function(ev){
				ev.preventDefault();
				var url=jQuery(this).closest('div').attr('data-url');
				jQuery('.elrte_editor').elrte()[0].elrte.selection.insertText('<img src="'+url+'"/>');
			})
			tpl.find('.thumbnail').each(function(){
                var f=jQuery(this).data('thumb_field');
                jQuery(this).attr('src',f?f:row['thumb_url']);
			})
			tpl.find('.image_preview').each(function(){
				jQuery(this).attr('src',act+'&view=true&'+
					self.element.attr('name')+'_save_action='+
					jQuery(this).closest('div').attr('rel')
				);
			})
			tpl.find('.view_doc').click(function(ev){
				ev.preventDefault();
				jQuery(this).univ().newWindow(act+'&view=true&'+
					self.element.attr('name')+'_save_action='+
					jQuery(this).closest('div').attr('rel')
				);
			})
			tpl.find('.save_doc').click(function(ev){
				ev.preventDefault();
				jQuery(this).univ().location(act+'&'+
					self.element.attr('name')+'_save_action='+
					jQuery(this).closest('div').attr('rel')
				);
			});
			tpl.appendTo(tb);
		});
		self.updateToken();
		var files=jQuery("#"+this.element.attr('name')+"_files").find('.files-container').children('div').not('.template').length;
		if(files>=this.options.multiple)this.element.hide();

	},
	removeFiles: function(ids){
		var tb=jQuery("#"+this.element.attr('name')+"_files").find('.files-container');
		var self=this;
		jQuery.each(ids,function(junk,id){
			tb.find('[rel='+id+']').remove();
		});
		self.updateToken();
		this.element.trigger('file_remove');
		this.element.show();
	},
	updateToken: function(){
		var tb=jQuery("#"+this.element.attr('name')+"_files").find('.files-container');
		var ids=[];
		tb.find('div').not('.template').each(function(){
			ids.push(jQuery(this).attr('rel'));
		});
		jQuery("#"+this.element.attr('name')+"_token").val(ids.join(','));
	},
	uploadComplete: function(data){
		// This method is called when iFrame upload is complete
		jQuery(this.options.form).atk4_form('clearError', this.element);
		if(!data){
			console.error('File upload was completed but no action was defined.'); 
			return;
		}
		jQuery('#'+this.name+'_progress').remove();
        this.element.data(data);
		this.element.trigger('upload');
		this.element.attr('disabled',false);
		this.addFiles([data]);
		//this.element.next('br').remove();
		this.element.remove();
		this._setChanged();
		this.element.trigger('upload_complete');
		//jQuery('#'+this.name+'_token').val(data.id);
	},
	uploadFailed: function(message,debug){
		if(debug){
			jQuery.univ().successMessage('Debug: '+jQuery.univ().toJSON(debug));
		}
		jQuery(this.options.form).atk4_form('fieldError',this.element,message);
		jQuery('#'+this.name+'_progress').remove();
		this.element.next().show()
		this.element.remove();
	},
	completeSWF: function(a,b,c,d,e){
		var token={
			'fileInfo':c,
			'filename':d
		}
		try{
			jQuery('#'+this.name+'_token').val(jQuery.univ().toJSON(token));
			this.element.after('File: '+token.fileInfo.name+' uploaded successfuly <br/>');
			this._setChanged();
		}catch(e){
			console.log(e);
		}
		console.log('hoho');
		return true;
	}

});

