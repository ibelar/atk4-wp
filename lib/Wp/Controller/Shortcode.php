<?php

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
	 *
	 * When running a shortcode, the app will return the html value instead of echoing it.
	 *
	 * Then shortcode are register as panels in order to get ajax action running smoothly.
	 *
	 * 2015-112-10 Enqueue shortcode js and css file after adding shortcode to app in order to load them after atk - js file.
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

	public function increaseShortcodeInstance( $key, $step = 1 )
	{
		$this->shortcodes[$key]['number'] += $step;
	}

	public function getShortcodeInstance( $key )
	{
		return $this->shortcodes[$key]['number'];
	}
}