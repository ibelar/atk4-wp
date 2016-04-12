<?php

/**
 *
 * WpShortcode are WpPanel extension.
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