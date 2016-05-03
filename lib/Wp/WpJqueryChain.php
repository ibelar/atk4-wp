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
 *
 *
 */
class Wp_WpJqueryChain extends jQuery_Chain
{
	public $jQueryVar;

	public function init()
	{
		parent::init();
		$this->jQueryVar = WpHelper::getJQueryVar();
	}

	/**
	 * Chain binds to parent object by default. Use this to use other selector $('selector')
	 *
	 * @param mixed $selector
	 *
	 * @return $this
	 */
	public function _selector($selector = null)
	{
		if ($selector === false) {
			$this->library = null;
		} elseif ($selector instanceof self) {
			$this->library = "{$this->jQueryVar}(".$selector.")";
		} else {
			$this->library = "{$this->jQueryVar}(".json_encode($selector).")";
		}

		return $this;
	}

	/**
	 * Use this to bind chain to document $(document)...
	 *
	 * @return $this
	 */
	public function _selectorDocument()
	{
		return $this->_library("{$this->jQueryVar}(document)");
	}

	/**
	 * Use this to bind chain to window $(window)...
	 *
	 * @return $this
	 */
	public function _selectorWindow()
	{
		return $this->_library("{$this->jQueryVar}(window)");
	}

	/**
	 * Use this to bind chain to "this" $(this)...
	 *
	 * @return $this
	 */
	public function _selectorThis()
	{
		return $this->_library("{$this->jQueryVar}(this)");
	}

	/**
	 * Use this to bind chain to "region" $(region). Region is defined by ATK when reloading.
	 *
	 * @return $this
	 */
	public function _selectorRegion()
	{
		return $this->_library("{$this->jQueryVar}(region)");
	}

	/**
	 * Render and return js chain as string
	 *
	 * @return string
	 */
	public function _render()
	{
		$ret = $this->prepend;
		if ($this->library) {
			$ret .= $this->library;
		} else {
			if ($this->str) {
				$ret .= "{$this->jQueryVar}('#".$this->owner->getJSID()."')";
			}
		}
		$ret .= $this->str;

		if ($this->enclose === true) {
			if ($this->preventDefault) {
				$ret = 'function(ev,ui){ev.preventDefault();ev.stopPropagation(); '.$ret.'}';
			} else {
				$ret = 'function(ev,ui){'.$ret.'}';
			}
		} elseif ($this->enclose) {
			$ret = ($this->library ?: "{$this->jQueryVar}('#".$this->owner->getJSID()."')").
			       ".bind('".$this->enclose."',function(ev,ui){ev.preventDefault();ev.stopPropagation(); ".$ret.'})';
		}

		if (@$this->debug) {
			echo "<font color='blue'>".htmlspecialchars($ret).';</font><br/>';
			$this->debug = false;
		}

		return $ret;
	}
}