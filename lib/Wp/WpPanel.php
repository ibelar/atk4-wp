<?php

/**
 * Wp Panel
 * Base view for Wordpress Panel, metaBox and shortcode.
 */
class Wp_WpPanel extends AbstractView
{
	//the id of the panel
	public $id;

	// A controller to handle ajax request.
	public $ajaxCtrl;

	public function init()
	{
		$this->app->page_object=$this;
		parent::init();
	}

	function defaultTemplate()
	{
		return ['wp-panel'];
	}

	function recursiveRender()
	{
		if(isset($_GET['cut_page']) && !isset($_GET['cut_object']) && !isset($_GET['cut_region']))
			$_GET['cut_object']=$this->short_name;

		parent::recursiveRender();
	}
}