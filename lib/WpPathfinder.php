<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-11
 * Time: 2:38 PM
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
		$this->api->pathfinder=$this;

		$this->addDefaultLocations();
		$this->addPluginLocations( $this->app->config_location);

		$this->_initialized = true;
	}

	public function addDefaultLocations()
	{

		//$base_directory= dirname(dirname(__FILE__));
		//$base_directory=

		$base_directory = dirname(__DIR__);
		$atk_base_path = $base_directory . '/vendor/atk4/atk4';
		$wp_public_path = $base_directory . '/public';
		//$atk_public_url = plugin_dir_url(__DIR__ ) . 'vendor/atk4/atk4/public/atk4';
		//$atk_public_url = str_replace( site_url(), '', $atk_public_url);
		$atk_public_url = content_url(). '/atk4-wp/vendor/atk4/atk4/public/atk4';
		$atk_public_url = str_replace( site_url(), '', $atk_public_url);
		//$wp_public_url = plugin_dir_url(__DIR__) . 'public';
		$wp_public_url = content_url().'/atk4-wp/public';
		$wp_public_url = str_replace( site_url(), '', $wp_public_url);

		$templates_folder=array('template','templates');

		if ($this->app->compat_42 && is_dir($base_directory.'/templates/default')) {
			$templates_folder='templates/default';
		}

		$this->base_location=$this->addLocation(array(
			'php'=>'lib',
			'page'=>'page',
			'tests'=>'tests',
			'template'=>$templates_folder,
			'mail'=>'mail',
			'logs'=>'logs',
			'dbupdates'=>'doc/dbupdates',
		))->setBasePath($base_directory);



		$this->atk_location=$this->addLocation(array(
			'php'=>'lib',
			'template'=>$templates_folder,
			'tests'=>'tests',
			'mail'=>'mail',
		))
		                         ->setBasePath($atk_base_path)
		;

		$this->public_location=$this->addLocation(array(
			'public'=>'',
			'js'=>'js',
			'css'=>'css',
		))
		                            ->setBasePath($atk_base_path.'/public/atk4')
		                            ->setBaseURL($atk_public_url);

		$this->wpPublicLocation=$this->addLocation(array(
			'public'=> '',
			'js'=> [ 'js', 'wpatk4/js' ],
			'css'=> ['css', 'wpatk4/css']
		))
		                            ->setBasePath($wp_public_path)
		                            ->setBaseURL($wp_public_url);


		if (@$this->api->pm) {
			// Add public location - assets, but only if
			// we hav PageManager to identify it's location
			if (is_dir($base_directory.'/public')) {
				$this->public_location=$this->addLocation(array(
					'public'=>'',
					'js'=>'js',
					'css'=>'css',
				))
				                            ->setBasePath($base_directory.'/public')
				                            ->setBaseURL($this->api->pm->base_path);
			} else {
				$this->base_location
					->setBaseURL($this->api->pm->base_path);
				$this->public_location = $this->base_location;
				$this->public_location->defineContents(array('js'=>'templates/js','css'=>'templates/css'));
			}

			if (basename($this->api->pm->base_path)=='public') {
				$this->base_location
					->setBaseURL(dirname($this->api->pm->base_path));
			}
		}

		if ($this->api->hasMethod('addSharedLocations')) {
			$this->api->addSharedLocations($this, $base_directory);
		}

		// Add shared locations
		if (is_dir(dirname($base_directory).'/shared')) {
			$this->shared_location=$this->addLocation(array(
				'php'=>'lib',
				'addons'=>'addons',
				'template'=>$templates_folder,
			))->setBasePath(dirname($base_directory).'/shared');
		}



		if (@$this->api->pm) {
			if ($this->app->compat_42 && is_dir($this->public_location->base_path.'/atk4/public/atk4')) {
				$this->atk_public=$this->public_location->addRelativeLocation('atk4/public/atk4');
			} elseif (is_dir($this->public_location->base_path.'/atk4')) {
				$this->atk_public=$this->public_location->addRelativeLocation('atk4');
			} elseif (is_dir($base_directory.'/vendor/atk4/atk4/public/atk4')) {
				$this->atk_public=$this->base_location->addRelativeLocation('vendor/atk4/atk4/public/atk4');
			} elseif (is_dir($base_directory.'/../vendor/atk4/atk4/public/atk4')) {
				$this->atk_public=$this->base_location->addRelativeLocation('../vendor/atk4/atk4/public/atk4');
			} else {
				echo $this->public_location;
				throw $this->exception('Unable to locate public/atk4 folder', 'Migration');
			}

			$this->atk_public->defineContents(array(
				'public'=>'.',
				'js'=>'js',
				'css'=>'css',
			));
		}
	}


	public function addPluginLocations( $pluginPath )
	{
		$templates_folder=array('template','templates');

		$pluginPublicPath = $pluginPath . 'public';
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
		))
		                                   ->setBasePath($pluginPath);

		$this->pluginPublicLocation = $this->addLocation(array(
			'public'=> '',
			'js'=> [ 'js' ],
			'css'=>'css',
		))
		                             ->setBasePath($pluginPublicPath)
		                             ->setBaseURL($pluginPublicUrl);
	}
}