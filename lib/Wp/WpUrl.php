<?php

class Wp_WpUrl extends URL
{

	public $wpAdminAjaxUrl;

	public function init()
	{
		AbstractModel::init();
		$this->addStickyArguments();
		$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
		/*if (is_admin() ){*/
			$this->setBaseURL( admin_url( 'admin-ajax.php') );
		/*} else {
			$this->setBaseURL( ajaxurl );
		}*/
	}

	/*public function setWpPage()
	{
		if( is_admin() ){
			$url = $this->wpAdminAjaxUrl;
		}
		return $url;
	}*/
}
