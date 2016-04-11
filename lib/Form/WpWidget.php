<?php

/**
 *  Use Custom form.
 *  Wp_Custom_Form does not contain <form> tag since Wp has already output it.
 *  Use of this form is simply a way to easily add field with a widget and continue using fonctionality of a Atk Form Class.
 */
class Form_WpWidget extends Wp_Custom_Form
{
	public $fieldCSSDefaultClass = 'widefat';
	public $fieldCssSpecialClasses = [ 'number' => 'tiny-text', 'checkbox' => 'checkbox', 'hidden' => ''];
	public $customFieldType  = ['line', 'checkbox', 'dropdown', 'number', 'valuelist','date'];

	public function init()
	{
		parent::init();
		//no need for js widget.
		$this->js_widget = null;
	}

	public function defaultTemplate( )
	{
		return ['custom/widget-form'];
	}
}