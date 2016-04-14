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

	public $shortcodes = [];
	/**
	 * Load metaBoxes define in our config file.
	 */
	public function loadShortcodes()
	{
		$shortcodes = $this->app->getConfig('shortcode', null);
		if( isset( $shortcodes )){
			foreach( $shortcodes as $key => $shortcode ){
				$this->registerShortcode( $key, $shortcode );
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
	public function registerShortcode( $key, $shortcode )
	{
		$shortcode['key'] = $key;
		$shortcode['number'] = 0;
		$app = $this->app;
		add_shortcode( $shortcode['name'], function ( $args ) use ( $key, $shortcode, $app ) {
			if ( $shortcode['atkjs'] ){
				$app->enqueueCtrl->enqueueAtkJsInFront();
			}
			if ( isset($shortcode['js'])){
				$app->enqueueCtrl->enqueueFiles( $shortcode['js'], 'js', ['start-atk4']);
			}
			if ( isset($shortcode['css'])){
				$app->enqueueCtrl->enqueueFiles( $shortcode['css'], 'css');
			}
			return $app->wpShortcodeExecute( $shortcode, $args );
		});


		//add this shortcode to our panel list.
		//This will allow to get ajax working.
		$this->app->panelCtrl->setPanels( $key, $shortcode );
		$this->app->panelCtrl->registerPanelHook( $key , $key );
		$this->shortcodes[$key] = $shortcode;

	}

	/**
	 * Every time Wp need to output a shortcode, the number of instance output will be increase.
	 * Keep track of the number of shortcode instance has been output by Wp.
	 * @param $key
	 * @param int $step
	 */
	public function increaseShortcodeInstance( $key, $step = 1 )
	{
		$this->shortcodes[$key]['number'] += $step;
	}

	/**
	 * Get the instance number of a shortcode.
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getShortcodeInstance( $key )
	{
		return $this->shortcodes[$key]['number'];
	}
}