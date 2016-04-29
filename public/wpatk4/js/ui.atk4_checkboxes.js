/* Welcome to Agile Toolkit JS framework. This file implements checkboxes / selectable on Grid.*/
jQuery.widget("ui.atk4_checkboxes", {
	_init: function(options){
		var chb=this;
		var ivalue=jQuery(this.options.dst_field).val();
		
		try{
			if(jQuery.parseJSON){
				ivalue=jQuery.parseJSON(ivalue);
				if(!ivalue)ivalue=[];
			}else{
				ivalue=eval('('+ivalue+')')
			}
		}catch(err){
			ivalue=[];
		}
		jQuery.each(ivalue,function(k,v){
			ivalue[k]=String(v);
		});
		
		
		this.element.find('tbody').selectable({filter: 'tr',stop: function(){ chb.stop.apply(chb,[this]) }}).css({cursor:'crosshair'});
		this.element.find('input[type=checkbox]')
		.each(function(){
			var o=jQuery(this);
			if(jQuery.inArray(o.val(), ivalue)>-1){
				o.prop('checked',true);
				jQuery(this).closest('tr').addClass('ui-selected');
			}
		})
		.change(function(){
			var tr=jQuery(this).closest('tr');
			if(jQuery(this).prop('checked')){
				tr.addClass('ui-selected');
			}else{
				tr.removeClass('ui-selected');
			}
			chb.recalc();
		});
	},
	stop: function(c){
		jQuery(c).children('.ui-selected').find('input').prop('checked',true);
		jQuery(c).children().not('.ui-selected').find('input').prop('checked',false);
		this.recalc();
	},
	select_all: function(){
		this.element.find('tbody tr').not('.ui-selected')
			.addClass('ui-selected')
			.find('input[type="checkbox"]').prop('checked',true);
		this.recalc();
	},
	unselect_all: function(){
		this.element.find('tbody tr.ui-selected')
			.removeClass('ui-selected')
			.find('input[type="checkbox"]').prop('checked',false);
		this.recalc();
	},
	recalc: function(){
		var r=[];
		this.element.find('input:checked').each(function(){
			r.push(jQuery(this).val());
		});
		if(this.options.dst_field){
			jQuery(this.options.dst_field).val(jQuery.univ.toJSON(r)).trigger('autochange_manual');
		}
	}
});
