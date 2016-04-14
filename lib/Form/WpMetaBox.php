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
 * Use custom form
 *  Wp_Custom_Form does not contain <form> tag since Wp has already output it.
 *  Use of this form is simply a way to easily add field within a metabox and continue using field functionality of a Atk Form Class.
 *
 * TODO Define our own metabox widget in order to handle ajax submit request for this form field only.
 */

class Form_WpMetaBox extends Wp_Custom_Form
{
	public function init()
	{
		parent::init();
		//no need for js metabox.
		$this->js_widget = null;
	}

	public function defaultTemplate( )
	{
		return ['custom/metabox-form'];
	}
}