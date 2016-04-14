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
 * FOR TESTING ONLY
 *
 */
class Wp_WpJqueryChain extends jQuery_Chain
{
	public $strExtension = '';
	public $wpAdminAjaxUrl;

	public function init()
	{
		parent::init();
		//$this->wpAdminAjaxUrl = admin_url( 'admin-ajax.php');
	}

	public function __call($name, $arguments)
	{
		$previousExtension = $this->strExtension;
		//$newExtension = "";
		if( isset( $arguments ) && !empty( $arguments ) ) {
			$a2 = $this->_flattern_objects($arguments, true);
			//$this->strExtension .= ".{$name}({$a2})";
			$newExtension = ".{$name}({$a2})";
		} else {
			//$this->strExtension .= ".{$name}()";
			$newExtension = ".{$name}()";
		}
		$this->strExtension = $previousExtension . $newExtension;
		return $this;
	}

	public function __get($property)
	{
		/* this enables you  to have syntax like this:
		 *
		 * $this->js()->offset()->top <-- access object items, if object is
		 * returned by chained method call */
		$newExtension = "";
		if (!property_exists($this, $property)) {
			//$this->strExtension .= ".{$property}";
			$newExtension = ".{$property}";
			$this->strExtension = $this->strExtension . $newExtension;
		}

		return $this;
	}

	public function _render()
	{
		$ret = $this->prepend;
		if ($this->library) {
			$ret .= $this->library;
		} else {
			if (!empty($this->strExtension)) {
				$jsid = $this->owner->getJSID();
				$ret .= "$('#{$jsid}')";
			}
		}
		$ret .= $this->strExtension;

		if ($this->enclose === true) {
			if ($this->preventDefault) {
				$ret = "function(ev,ui){ev.preventDefault();ev.stopPropagation(); {$ret}}";
			} else {
				$ret = "function(ev,ui){{$ret}}";
			}
		} elseif ($this->enclose) {
			$ret = ($this->library) ?: "$('#{$this->owner->getJSID()}').bind('{$this->enclose}',function(ev,ui){ev.preventDefault();ev.stopPropagation(); {$ret}})";
			//$ret = ($this->library ?: "$('#".$this->owner->getJSID()."')").".bind('".$this->enclose."',function(ev,ui){ev.preventDefault();ev.stopPropagation(); ".$ret.'})';
		}

		if (@$this->debug) {
			echo "<font color='blue'>".htmlspecialchars($ret).';</font><br/>';
			$this->debug = false;
		}

		return $ret;
	}

	protected function _flattern_objects($arg, $return_comma_list = false)
	{
		/*
		 * This function is very similar to json_encode, however it will traverse array
		 * before encoding in search of objects based on AbstractObject. Those would
		 * be replaced with their json representation if function exists, otherwise
		 * with string representation
		 */
		$s = null;
		$assoc = false;
		if (is_object($arg)) {
			if ($arg instanceof self) {
				$s = $arg->_render();
				if (substr($s, -1) == ';') {
					$s = substr($s, 0, -1);
				}
			} elseif ($arg instanceof AbstractView) {
				$jsid = $arg->getJSID();
				$s =  "'#{$jsid}'";
			} else {
				$safeJs = $this->_safe_js_string((string) $arg);
				$s = "'{$safeJs}'";    // indirectly call toString();
			}
		} elseif (is_array($arg)) {
			$a2 = array();
			// is array associative? (hash)
			//$test =  array_values($arg);
			//$assoc = $arg != array_values($arg);
			if ( $this->isAssoc($arg)){
				$assoc = true;
			}
			foreach ($arg as $key => $value) {
				$value = $this->_flattern_objects($value);
				$key = $this->_flattern_objects($key);
				if (!$assoc || $return_comma_list) {
					$a2[] = $value;
				} else {
					$a2[] = $key.':'.$value;
				}
			}
			if ($return_comma_list) {
				$s = implode(',', $a2);
			} elseif ($assoc) {
				$s = '{'.implode(',', $a2).'}';
			} else {
				$s = '['.implode(',', $a2).']';
			}
		} elseif (is_string($arg)) {
			$safeJs = $this->_safe_js_string($arg);
			$s = "'{$safeJs}'";
		} elseif (is_bool($arg)) {
			$s = json_encode($arg);
		} elseif (is_numeric($arg)) {
			$s = json_encode($arg);
		} elseif (is_null($arg)) {
			$s = json_encode($arg);
		} else {
			throw $this->exception('Unable to encode value for jQuery Chain - unknown type')
			           ->addMoreInfo('arg', $arg);
		}

		return $s;
	}


	function isAssoc($arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	/**
	 * Execute more JavaScript code before chain. Avoid using.
	 *
	 * @param [type] $code [description]
	 *
	 * @return [type] [description]
	 */
	public function _prepend($code)
	{
		if (is_array($code) && !empty( $code )) {
			$code = implode(';', $code);
		}
		$this->prepend = $code.';'.$this->prepend;

		return $this;
	}

	/* [private] used by custom json_encoding */
	public function _safe_js_string($str)
	{
		return $str;
		/*$l = strlen($str);
		$ret = '';
		for ($i = 0; $i < $l; ++$i) {
			switch ($str[$i]) {
				case "\r":
					$ret .= '\\r';
					break;
				case "\n":
					$ret .= '\\n';
					break;
				case '"':
				case "'":
				case '<':
				case '>':
				case '&':
				case '\\':
					$ret .= '\x'.dechex(ord($str[$i]));
					break;
				default:
					$ret .= $str[$i];
					break;
			}
		}

		return $ret;*/
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
//		if(isset($_POST['ajax_submit']) || $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'){
//			//if($this->api->jquery)$this->api->jquery->getJS($this->owner);
//
//			/*if(headers_sent($file,$line)){
//				echo "<br/>Direct output (echo or print) detected on $file:$line. <a target='_blank' "
//				     ."href='http://agiletoolkit.org/error/direct_output'>Use \$this->add('Text') instead</a>.<br/>";
//			}*/
//
//
//			$x=$this->api->template->get('document_ready');
//			if(is_array($x))$x=join('',$x);
//			echo $this->_render();
//			$this->api->hook('post-js-execute');
//			exit;
//		}else{
//			throw $this->exception('js()->..->execute() must be used in response to form submission or AJAX operation only');
//		}
		if (isset($_POST['ajax_submit']) || $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			//if($this->app->jquery)$this->app->jquery->getJS($this->owner);
			if (headers_sent($file, $line)) {
				echo "<br/>Direct output (echo or print) detected on $file:$line. <a target='_blank' "
				     ."href='http://agiletoolkit.org/error/direct_output'>Use \$this->add('Text') instead</a>.<br/>";
			}
			$x = $this->app->template->get('document_ready');
			if (is_array($x)) {
				$x = implode('', $x);
			}
			echo $this->_render();
			$this->app->hook('post-js-execute');
			exit;
		} else {
			throw $this
				->exception('js()->..->execute() must be used in response to form submission or AJAX operation only');
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