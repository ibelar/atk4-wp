<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-24
 * Time: 2:13 PM
 *
 * This controller is responsable for euqueue necessary atk file.
 *
 * If using an atk Panel in admin area of Wordpress, will load the necessary
 * atk js and css files.
 * Will also check for extra file to be load if they are set in config file. 
 *
 * //TODO filter enqueue files according to panel
 */
class Wp_Controller_Enqueue extends AbstractController
{
	protected $atkJsFiles = ['jquery-ui-1-11-4.min', /*'wp-init',*/ 'start-atk4',
		'ui.atk4_loader', 'ui.atk4_notify', 'atk4_univ_basic',
		'atk4_univ_jui', 'wp-atk4_univ_ext', 'wp-atk4' ];

	protected $atkCssFiles = [ 'wp-atk4' ];

	public function init()
	{
		parent::init();


		$this->registerAtkJsFiles( $this->atkJsFiles );

		add_action('admin_enqueue_scripts', [&$this, 'enqueueAdminFiles']);

		// check if we need to enqueue files in front end.
		if ( ! is_admin() ){
			$this->enqueueFrontFiles();
		}


	}

	/**
	 * @return array
	 */
	public function getAtkJsFiles() {
		return $this->atkJsFiles;
	}

	public function registerAtkJsFiles ( $files )
	{
		//Register wp-init as a dependency
		// This ensure that wp-init will be load first when atkjs files are needed.
		wp_enqueue_script('wp-init', $this->api->locateURL('js', 'wp-init.js'), ['jquery']);

		//register remaining files.
		foreach ( $files as $file){
			//atkjs file need wp-init as a dependency.
			wp_register_script( $file, $this->api->locateURL('js',$file . '.js'), ['wp-init'] );
		}
	}


	public function enqueueAdminFiles( $hook )
	{
		//Check if this is an atk panel.
		// and enqueue atk file
		$panel = $this->getAtkPanel( $hook );
		if ( isset($panel) ){
			//check if panel require specific js file.
			if ( isset ($panel['js'])){
				$this->atkJsFiles = array_merge($this->atkJsFiles, $panel['js'] );
			}

			if ( @$userJsFiles = $this->api->getConfig('enqueue/admin/js', null)){
				$this->atkJsFiles = array_merge($this->atkJsFiles, $userJsFiles );
			}
			$this->enqueueFiles( $this->atkJsFiles,  'js');

			if ( isset ($panel['css'])){
				$this->atkCssFiles = array_merge($this->atkCssFiles, $panel['css'] );
			}

			if ( @$userCssFiles = $this->api->getConfig('enqueue/admin/css', null)){
				$this->atkCssFiles = array_merge($this->atkCssFiles, $userCssFiles );
			}
			$this->enqueueFiles( $this->atkCssFiles,  'css');
		}

	}

	public function enqueueAtkJsInFront()
	{
		$this->enqueueFiles( $this->atkJsFiles,  'js');
		$this->enqueueFiles( $this->atkCssFiles,  'css');
	}

	public function enqueueFrontFiles()
	{
		if ( @$frontJsFiles = $this->api->getConfig('enqueue/front/js', null)){
			$this->enqueueFiles($frontJsFiles, 'js');
		}
		if (@$frontCssFiles = $this->api->getConfig('enqueue/front/css', null)){
			$this->enqueueFiles($frontCssFiles, 'css');
		}
	}

	public function enqueueFiles( $files, $type, $required = null )
	{
		if (!isset($required))
			$required = ['wp-init'];
		try {
			if ( $type === 'js'){
				foreach ( $files as $file){
					//atkjs file need wp-init as a dependency.
					wp_enqueue_script( $file, $this->api->locateURL('js',$file . '.js'), $required );
				}
			} else {
				foreach ( $files as $file){
					wp_enqueue_style( $file, $this->api->locateURL('css',$file . '.css') );
				}
			}
		} catch (Exception $e) {
			// Handles output of the exception
			$this->api->caughtException($e);
		}

	}

	public function isAtkPanel ($hook )
	{
		return $this->app->panelCtrl->isAtkPanel( $hook );
	}

	public function getAtkPanel ($hook )
	{
		return $this->app->panelCtrl->getAtkPanel ( $hook );
	}
}