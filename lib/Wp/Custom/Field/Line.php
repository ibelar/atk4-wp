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
 * Line field in Wordpress custom form.
 */
class Wp_Custom_Field_Line extends Wp_Custom_Field
{
	public function getInput($attr=array())
	{
		return parent::getInput(array_merge(array('type' => 'text'), $attr));
	}
}