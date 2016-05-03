/* Welcome to Agile Toolkit JS framework. This file implements Hint. */

// The following HTML structure should be used:
//
// <table>     <!-- binding to this element -->
//   <thead>
//     <th>..
//    </thead>
//   <tr rel="23">	<!-- id of this row, important! -->
//      <td class="grid_cell">
//      <td class="grid_cell">
//

jQuery.widget("ui.atk4_reference", {
    options: {
    },
	shown: true,
	autocomplete: null,
    _init: function(options){
        jQuery.extend(this.options,this.default_options,options);
		this.name=this.element.attr('id');
		this.element.css({cursor: 'pointer'});
		//var def=this.element.prepend('<option'
			//+(this.element.find('option[selected]').length?'':' selected')+'> .. </option');
	},
	showAddDialog: function(){

	},
	initAutocomplete: function(ac_options){
		// Add new field after ourselves for auto-complete
		//var t=jQuery('<span class="input_autocomplete wo_button"><span><i></i><input class="input_style1" id="'+this.name+'_autocomplete"/></span></span>');
		this.element.combobox();



		var dropdown=this.element.prev();

		this.autocomplete=jQuery('#'+this.name+'_autocomplete');

        this.autocomplete.autocomplete();
        this.element.addClass('field_reference');
	},
	isNewEntry: function(el){
		var val=jQuery(el).val();
		var ref=this;

		// Not adding empty entries
		if(!val && ref.element.val()!=''){
			ref.element.val('');
			if(ref.element.attr('data-initvalue')!=ref.element.val()){
				ref.element.attr('data-initvalue',ref.element.val());
				ref.element.trigger('change_ref');
			}
			return false;
		}

		// Matches selected entry
		if(this.element.find(':selected').text() == val)return false;

		var matches_other=false;
		this.element.children().each(function(){
			if(jQuery(this).text()==val){
				ref.element.val(jQuery(this).val());
				if(ref.element.attr('data-initvalue')!=ref.element.val()){
					ref.element.attr('data-initvalue',ref.element.val());
					ref.element.trigger('change_ref');
				}
				matches_other=true;
			}
		});

		// Matches other entry
		if(matches_other)return false;

		if(ref.element.val()!=''){
			ref.element.val('');
		}
		return true;
	},
	fixBrokenAutocomplete: function(el){
		var ref=this;

		if(this.isNewEntry(el)){
		}else{
		}
	},
	setPlusUrl: function(url,options,title){
		this.options.plus_url=url;
		var ref=this;
		this.autocomplete.parent().after('<a href="'+this.options.plus_url+'" id="'+this.name+'_addlink" class="ref_model_add autocomplete_add gbutton button_style2">+</a>');
		this.autocomplete.parent().parent().removeClass('wo_button');

		jQuery('#'+this.name+'_addlink').click(function(ev){
			ev.preventDefault();
			jQuery(this).univ().dialogURL('Adding new '+(title?title:'..'),ref.options.plus_url
				+"&val_txt="+escape(ref.autocomplete.val())
				+"&val_id="+escape(ref.element.val())
				,options,function(){
				region.find('.fill_current').val(ref.element.val()).change();
			});
		});
	},
	getData: function(){
		var data=[];
		this.element.children('option').each(function(key,val){
			if(jQuery(val).val()){
				data.push([jQuery(val).val(),jQuery.trim(jQuery(val).text())]);
			}
		});
		return data;
	}
});
