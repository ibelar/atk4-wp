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
 *
 * WpShortcode
 * Shortcode adapter between Wordpress and atk.
 *
 */

class Wp_WpShortcode extends Wp_WpPanel
{
	//public $needAtkJs = false;

	//Argument passed via shortcode
	public $args = null;

	public function init()
	{
		parent::init();
		$this->args = $this->owner->shortcode['args'];

		//normalize name for ajax call.
		if( $this->app->ajaxMode && @$num = $_GET['atkshortcode'] ){
			if( $num > 1 ){
				$this->short_name   = $this->short_name . '_' . $_GET['atkshortcode'];
				$this->name         = $this->name . '_' . $_GET['atkshortcode'];
			}
		}
	}

	public function defaultTemplate()
	{
		return ['wp-front'];
	}
}