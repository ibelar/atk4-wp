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

	public function init()
	{
		parent::init();
		//$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
	}

	/**
	 * Modified 2016-01-18: header_sent response true in WP. Needed to remove it for form to work. Header are handle by WP, so probably do not need to check.
	 *
	 * Send chain in response to form submit, button click or ajaxec() function for AJAX control output
	 *
	 * @return [type] [description]
	 */
	function execute()
	{
		if(isset($_POST['ajax_submit']) || $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'){
			//if($this->api->jquery)$this->api->jquery->getJS($this->owner);

			/*if(headers_sent($file,$line)){
				echo "<br/>Direct output (echo or print) detected on $file:$line. <a target='_blank' "
				     ."href='http://agiletoolkit.org/error/direct_output'>Use \$this->add('Text') instead</a>.<br/>";
			}*/


			$x=$this->api->template->get('document_ready');
			if(is_array($x))$x=join('',$x);
			echo $this->_render();
			$this->api->hook('post-js-execute');
			exit;
		}else{
			throw $this->exception('js()->..->execute() must be used in response to form submission or AJAX operation only');
		}
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