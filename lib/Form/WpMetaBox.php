<?php

/**
 * Created by abelair.
 * Date: 2016-04-06
 * Time: 10:27 AM
 */
class Form_WpMetaBox extends Wp_Custom_Form
{
	public function init()
	{
		parent::init();
		//no need for js metabox.
		//TODO Define our own metabox widget in order to handle ajax submit request for this form field only.
		// We will need to redefine how form is construct in widget and apply only for the form field, not the entire post form.
		// Otherwise post.php get call instead of our ajax call.
		// If we are not being bound to form directly, then find form inside ourselves
		/*if(!this.form.is('form')){
			this.form=this.form.find('form');
			this.element.bind('submit',function(ev){
				ev.preventDefault();
				self.submitForm();
			});
		}*/
		$this->js_widget = null;
	}

	public function defaultTemplate( )
	{
		return ['custom/metabox-form'];
	}
}