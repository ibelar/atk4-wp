<?php

/**
 * Class Wp_WpUrl
 * Set Base url value for Wordpress.
 * Bypass atk parent init in order to avoid exception because we are not using page manager.
 */
class Wp_WpUrl extends URL
{

	public $wpAdminAjaxUrl;
	public $wpAdminUrl;

	public function init()
	{
		//bypass parent::init()
		AbstractModel::init();
		$this->addStickyArguments();
		$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
		$this->wpAdminUrl = admin_url('admin.php');

		//use ajax as default
		$this->setBaseURL( $this->wpAdminAjaxUrl );

	}
}
