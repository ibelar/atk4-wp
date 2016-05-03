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
 * This controller is responsible for registering shortcode.
 *
 */


class Wp_Controller_Shortcode extends AbstractController
{

	public $shortcodes      = [];
	public $hasLoadAtkJs    = false;

	/**
	 * @return array
	 */
	public function getShortcodes() {
		return $this->shortcodes;
	}

	/**
	 * Load Shortcode define in our config file.
	 */
	public function loadShortcodes()
	{
		$shortcodes = $this->app->getConfig('shortcode', null);
		if( isset( $shortcodes )){
			foreach ($shortcodes as $key => $shortcode) {
				$this->registerShortcode($key, $shortcode);
			}
		}
	}

	/**
	 * Register shortcode within wordpress
	 * Add js and css file if need.
	 *
	 * @param $key
	 * @param $shortcode
	 */
	public function registerShortcode($key, $shortcode)
	{
		$shortcode['key'] = $key;
		$shortcode['number'] = 0;
		$app = $this->app;
		add_shortcode($shortcode['name'], function($args) use ($key, $shortcode, $app) {
			//Load js and css file on first run.
			if ($app->shortcodeCtrl->getShortcodeInstance($key) === 0) {
				if ($shortcode['atkjs'] && !$this->hasLoadAtkJs) {
					$app->enqueueCtrl->enqueueAtkJsInFront();
					$this->hasLoadAtkJs = true;
				}
				if (isset($shortcode['js'])) {
					$app->enqueueCtrl->enqueueFiles($shortcode['js'], 'js');
				}
				if (isset($shortcode['js-inc'])) {
					$app->enqueueCtrl->enqueueJsInclude($shortcode['js-inc']);
				}
				if (isset($shortcode['css'])) {
					$app->enqueueCtrl->enqueueFiles($shortcode['css'], 'css');
				}
			}
			return $app->wpShortcodeExecute($shortcode, $args);
		});


		//add this shortcode to our panel list.
		//This will allow to get ajax working.
		$this->app->panelCtrl->setPanels($key, $shortcode);
		$this->app->panelCtrl->registerPanelHook($key, $key);
		$this->shortcodes[$key] = $shortcode;

	}

	/**
	 * Will normalize shortcode name for html id attribute output.
	 * If more than one shortcode is set to be display on a page
	 * this will set the attribute id by adding '_N' where n is the current number of
	 * the shortcode.
	 *
	 * Needed for proper ajax action to work within the shortcode.
	 *
	 * @param $key
	 *
	 * @return null|string
	 */
	public function normalizeShortCodeName($key)
	{
		$name = null;
		if ($this->shortcodes[$key]['number'] > 1) {
			$name = $key.'_'.$this->shortcodes[$key]['number'];
		} else {
			$name = $key ;
		}
		return $name;
	}

	/**
	 * Every time Wp need to output a shortcode, the number of instance output will be increase.
	 * Keep track of the number of shortcode instance has been output by Wp.
	 * @param $key
	 * @param int $step
	 */
	public function increaseShortcodeInstance($key, $step = 1)
	{
		$this->shortcodes[$key]['number'] += $step;
	}

	/**
	 * Get the instance number of a shortcode.
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getShortcodeInstance($key)
	{
		return $this->shortcodes[$key]['number'];
	}
}