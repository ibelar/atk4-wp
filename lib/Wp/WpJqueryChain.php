<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-26
 * Time: 10:21 AM
 */
class Wp_WpJqueryChain extends jQuery_Chain
{
	public $wpAdminAjaxUrl;

	public function init() {
		parent::init();
		$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
	}

	/**
	 * Reload object
	 *
	 * You can bind this to custom event and trigger it if object is not
	 * directly accessible.
	 * If interval is given, then object will periodically reload itself.
	 *
	 * @param Array $arg
	 * @param jQuery_Chain $fn
	 * @param string $url
	 * @param integer $interval Interval in milisec. how often to reload object
	 *
	 * @return this
	 */
	/*function reload($arg = array(), $fn = null, $url = null, $interval = null) {
		if ($fn && $fn instanceof jQuery_Chain) {
			$fn->_enclose();
		}
		$obj = $this->owner;
		if (!$url) {
			$url = $this->api->url(null, array('cut_object' => $obj->name));
			//$url = $this->wpAdminAjaxUrl . '?cut_object=' . $obj->name;
		}
		return $this->univ()->_fn('reload', array($url, $arg, $fn, $interval));
	}*/
}