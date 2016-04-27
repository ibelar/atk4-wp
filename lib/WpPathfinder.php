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
 * Adapt atk pathfinder to Wordpress.
 */
class WpPathfinder extends PathFinder
{
	//Pathfinder object for atk4-wp public location
	public $wpPublicLocation;
	//Pathfinder object for plugin dir location
	public $pluginLocation;
	//Pathfinder object for plugin dir public location
	public $pluginPublicLocation;

	public function init()
	{
		$this->app->pathfinder = $this;

		$this->addDefaultLocations();
		$this->addPluginLocations($this->app->config_location);

		$this->_initialized = true;
	}

	public function addDefaultLocations()
	{

		//$base_directory= dirname(dirname(__FILE__));
		//$base_directory=

		$base_directory = dirname(__DIR__);
		//path to atk4 files
		$atk_base_path = $base_directory . '/vendor/atk4/atk4';
		//path to atk4-wp public files
		$wp_public_path = $base_directory . '/public';

		// url to atk4 public files
		// need to remove site url from url, otherwise, atk4 will output it twice.
		$atk_public_url = content_url(). '/atk4-wp/vendor/atk4/atk4/public/atk4';
		$atk_public_url = str_replace( site_url(), '', $atk_public_url);

		// url to atk4-wp public files
		// need to remove site url from url, otherwise, atk4 will output it twice.
		$wp_public_url = content_url().'/atk4-wp/public';
		$wp_public_url = str_replace(site_url(), '', $wp_public_url);

		$templates_folder = array('template','templates');

		if ($this->app->compat_42 && is_dir($base_directory.'/templates/default')) {
			$templates_folder = 'templates/default';
		}

		$this->base_location = $this->addLocation(array(
			'php'=>'lib',
			'page'=>'page',
			'tests'=>'tests',
			'template'=>$templates_folder,
			'mail'=>'mail',
			'logs'=>'logs',
			'dbupdates'=>'doc/dbupdates',
		))->setBasePath($base_directory);



		$this->atk_location = $this->addLocation(array(
			'php'=>'lib',
			'template'=>$templates_folder,
			'tests'=>'tests',
			'mail'=>'mail',
		))->setBasePath($atk_base_path);

		$this->public_location = $this->addLocation(array(
			'public'=>'',
			'js'=>'js',
			'css'=>'css',
		))->setBasePath($atk_base_path.'/public/atk4')
		  ->setBaseURL($atk_public_url);

		$this->wpPublicLocation = $this->addLocation(array(
			'public'=> '',
			'js'=> [ 'js', 'wpatk4/js' ],
			'css'=> ['css', 'wpatk4/css']
		))->setBasePath($wp_public_path)
		  ->setBaseURL($wp_public_url);

		$this->atk_public = $this->wpPublicLocation->addRelativeLocation('vendor/atk4/atk4/public/atk4');
		$this->atk_public->defineContents(array(
			'public'=>'.',
			'js'=>'js',
			'css'=>'css',
		));
	}


	/**
	 * Add pathfinder location for the plugin directory.
	 * $pluginPath usually equal to plugin_dir_path( __FILE__ ) and is also equivalent to the plugin config location
	 *
	 *
	 * @param $pluginPath
	 */
	public function addPluginLocations($pluginPath)
	{
		$templates_folder = array('template','templates');

		//path to plugin public directory
		$pluginPublicPath = $pluginPath . 'public';
		// url to plugin public directory.
		// need to remove site url from it.
		$pluginPublicUrl  = plugin_dir_url($pluginPath . 'public');
		$pluginPublicUrl  = str_replace( site_url(), '', $pluginPublicUrl . 'public');

		$this->pluginLocation = $this->addLocation(array(
			'php'=>'lib',
			'page'=>'page',
			'tests'=>'tests',
			'template'=>$templates_folder,
			'mail'=>'mail',
			'logs'=>'logs',
			'dbupdates'=>'doc/dbupdates',
		))->setBasePath($pluginPath);

		$this->pluginPublicLocation = $this->addLocation(array(
			'public'=> '',
			'js'=> [ 'js' ],
			'css'=>'css',
		))->setBasePath($pluginPublicPath)
		  ->setBaseURL($pluginPublicUrl);
	}
}