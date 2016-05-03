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
 * Adapt Agile Toolkit Jui to Wordpress
 *
 * 2016-04-29 Try to replace jquery alias $ to avoid conflict using Wp_WpJqueryChain class.
 *
 * We can replace alias to all of them except for addOnReady method on Jui class that use
 * a private property. We cannot override addOnReady because of that.
 * Solution would be to add a getter for the private var: atk4_initialised
 * or redefined the entire jUI class.
 */
class Wp_WpJui extends jQuery
{
	public $chain_class = 'Wp_WpJqueryChain';

	/**
	 * @var bool
	 */
	private $atk4_initialised = false;

	/** @var App_Web */
	public $app;

	/**
	 * Initialization
	 * Original
	 */
	public function init()
	{
		parent::init();
		if (@$this->app->jui) {
			throw $this->exception('Do not add jUI twice');
		}
		$this->app->jui = $this;

		$this->atk4_initialised = true;
	}

	public function addDefaultIncludes(){}


	/**
	 * Adds stylesheet
	 *
	 * @param string $file
	 * @param string $ext
	 * @param bool $template
	 *
	 * @return $this
	 */
	public function addStylesheet($file, $ext = '.css', $template = false)
	{
		$url = $this->app->locateURL('css', $file.$ext);
		if (!$this->atk4_initialised || $template) {
			return parent::addStylesheet($file, $ext);
		}

		parent::addOnReady(WpHelper::getJQueryVar().'.atk4.includeCSS("'.$url.'")');
	}

	public function addInclude($file, $ext='.js')
	{
		if (strpos($file, 'jquery') === false) {
			if (strpos($file, 'http') === 0) {
				$this->addOnReady(WpHelper::getJQueryVar().'.atk4.includeJS("'.$file.'")');
				return $this;
			}
			$url = $this->app->locateURL('js', $file.$ext);
			$this->addOnReady(WpHelper::getJQueryVar().'.atk4.includeJS("'.$url.'")');
		}
		return $this;
	}

	/**Original
	 * Adds JS chain to DOM onReady event
	 *
	 * @param jQuery_Chain|string $js
	 *
	 * @return $this
	 */
	public function addOnReady($js)
	{
		if ($js instanceof jQuery_Chain) {
			$js = $js->getString();
		}
		if (!$this->atk4_initialised) {
			return parent::addOnReady($js);
		}

		$this->app->template->append('document_ready', WpHelper::getJQueryVar().'.atk4(function(){ '.$js."; });\n");

		return $this;
	}
}