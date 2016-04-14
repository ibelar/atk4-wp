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
 * Widget controller for Wordpress
 *
 * Will load and register widget define in config-widget in Wordpress.
 * Widgets use a clone copy of WpAtk app ( widgetAtkApp )for defining their views
 * and form in order to output them.
 *
 * Note: Without cloning, view define for widget would interfere with those
 * define for panel when WpAtk is render in admin mode because widget view would be output
 * by WpAtk automatically. Therefore, we need a copy of WpAtk to build widget view and form.
 *
 */
class Wp_Controller_Widget extends AbstractController
{
	//Clone of this app for outputting html in views define for widget.
	protected $widgetAtkApp = null;

	public function init()
	{
		parent::init();
	}

	/**
	 * Get widgets defined in config-widget file
	 * and register them if found.
	 */
	public function loadWidgets()
	{
		$widgets = $this->app->getConfig('widget', null);
		if( isset($widgets)){
			foreach( $widgets as $key => $widget ){
				$this->registerWidget( $key, $widget );
			}
		}
	}

	/**
	 * Register each widget within Wordpress.
	 * Once register perform initialisation on them in order
	 * for Wordpress widget class to work with atk.
	 * @param $id
	 * @param $widget
	 */
	public function registerWidget( $id, $widget )
	{
		add_action( 'widgets_init', function() use ($id, $widget) {
			global $wp_widget_factory;
			register_widget( $widget['uses'] );
			//get latest create widget in widget factory
			$wdg = end($wp_widget_factory->widgets);
			// pre init latest widget.
			$wdg->beforeInit( $id, $widget, $this->getWidgetAtkApp() );
		});
	}

	/**
	 * Will return a singleton copy of WpAtk app use for widget display and form.
	 */
	private function getWidgetAtkApp()
	{
		if( !isset ($this->widgetAtkApp) ) {
			$this->widgetAtkApp = clone $this->app;
		}
		return $this->widgetAtkApp;
	}

}