<?php
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
 *  Use Custom form.
 *  Wp_Custom_Form does not contain <form> tag since Wp has already output it.
 *  Use of this form is simply a way to easily add field within a widget and continue using field functionality of an Atk Form Class.
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