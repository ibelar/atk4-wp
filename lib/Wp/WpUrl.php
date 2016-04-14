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
 * Class Wp_WpUrl
 * Set Base url value for Wordpress.
 *
 */
class Wp_WpUrl extends URL
{

	public $wpAdminAjaxUrl;
	public $wpAdminUrl;

	public function init()
	{
		//Bypass atk parent init in order to avoid exception because we are not using page manager.
		AbstractModel::init();
		$this->addStickyArguments();
		$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
		$this->wpAdminUrl = admin_url('admin.php');

		//use ajax as default
		$this->setBaseURL( $this->wpAdminAjaxUrl );

	}
}
