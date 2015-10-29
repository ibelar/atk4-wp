<?php

/**
 * Created by abelair.
 * Date: 2015-09-30
 * Time: 9:39 AM
 *
 * Wp_Shortcode are Panel extension in order to setup wp ajax using the proper atk-panel value.
 * They are register within the panel controller on add_shortcut wp action
 * and behave like panel except that when app generate a shortcode
 * it will do it using getHtml() instead of regular main.
 *
 *
 */
class Wp_WpShortcode extends Wp_WpPanel
{
	public $needAtkJs = false;

	public function init()
	{
		parent::init();

		if ( $this->needAtkJs ){
			//setup atkjs file.
			$this->app->enqueueCtrl->enqueueAtkJsInFront();
		}
	}
}