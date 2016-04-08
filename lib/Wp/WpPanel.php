<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-23
 * Time: 10:16 AM
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
		$this->app->template->trySet('panel', $this->id);
	}

	function defaultTemplate(){
		return array('wp-panel');
	}

	function recursiveRender(){
		if(isset($_GET['cut_page']) && !isset($_GET['cut_object']) && !isset($_GET['cut_region']))
			$_GET['cut_object']=$this->short_name;

		parent::recursiveRender();
	}
}