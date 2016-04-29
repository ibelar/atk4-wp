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
 * This controller is responsable for enqueuing necessary atk file.
 *
 * If using an atk Panel in admin area of Wordpress, will load the necessary
 * atk js and css files.
 * Will also check for extra file to be load if they are set in config file. 
 *
 * //TODO filter enqueue files according to panel
 */
class Wp_Controller_Enqueue extends AbstractController
{
	//todo see if we can use WP jquery ui core instead of our.
	/*protected $atkJsFiles = ['jquery-ui-1-11-4.min', 'start-atk4',
		'ui.atk4_loader', 'ui.atk4_notify', 'atk4_univ_basic',
		'atk4_univ_jui', 'wp-atk4_univ_ext' ];*/

	protected $atkJsFiles = ['jquery-ui-1-11-4.min', 'wp-atk4-bundle-jquery.min' ];

	protected $atkCssFiles = ['wp-atk4'];

	public function init()
	{
		parent::init();

		if (is_admin()) {
			add_action('admin_enqueue_scripts', [$this, 'enqueueAdminFiles']);
		} else {
			add_action('wp_enqueue_scripts', [$this, 'enqueueFrontFiles']);
		}
	}

	/**
	 * @return array
	 */
	public function getAtkJsFiles()
	{
		return $this->atkJsFiles;
	}

	public function registerAtkJsFiles($files)
	{
		//Register wp-init as a dependency
		// This ensure that wp-init will be load first when atkjs files are needed.
		//wp_enqueue_script('wp-init', $this->app->locateURL('js', 'wp-init.js'), ['jquery']);

		//register files.
		foreach ($files as $file) {
			//atkjs file need wp-init as a dependency.
			wp_register_script($file, $this->app->locateURL('js', $file.'.js'), ['jquery']);
		}
	}


	public function enqueueAdminFiles($hook, $forceEnqueue = false)
	{

		//Check if this is an atk panel.
		// and enqueue atk file
		$panel = $this->getAtkPanel($hook);
		if (isset($panel) || $forceEnqueue) {
			$this->registerAtkJsFiles($this->atkJsFiles);
			//check if panel require specific js file.
			if (isset ($panel['js'])) {
				$this->atkJsFiles = array_merge($this->atkJsFiles, $panel['js']);
			}

			if (@$userJsFiles = $this->app->getConfig('enqueue/admin/js', null)) {
				$this->atkJsFiles = array_merge($this->atkJsFiles, $userJsFiles);
			}
			$this->enqueueFiles($this->atkJsFiles, 'js');

			if (isset ($panel['css'])) {
				$this->atkCssFiles = array_merge($this->atkCssFiles, $panel['css']);
			}

			if (@$userCssFiles = $this->app->getConfig('enqueue/admin/css', null)) {
				$this->atkCssFiles = array_merge($this->atkCssFiles, $userCssFiles);
			}
			$this->enqueueFiles($this->atkCssFiles, 'css');
		}

	}

	public function enqueueAtkJsInFront()
	{
		$this->enqueueFiles($this->atkJsFiles, 'js');
		$this->enqueueFiles($this->atkCssFiles, 'css');
	}

	public function enqueueFrontFiles()
	{
		$this->registerAtkJsFiles($this->atkJsFiles);
		if (@$frontJsFiles = $this->app->getConfig('enqueue/front/js', null)) {
			$this->enqueueFiles($frontJsFiles, 'js');
		}
		if (@$frontCssFiles = $this->app->getConfig('enqueue/front/css', null)) {
			$this->enqueueFiles($frontCssFiles, 'css');
		}
	}

	public function enqueueFiles($files, $type, $required = null)
	{
		if (!isset($required))
			$required = ['jquery'];
		try {
			if ($type === 'js') {
				//if decide to load jquery ui already register with WP then we need to add each module one by one...
				/*
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-ui-datepicker');
				*/
				foreach ($files as $file) {
					//atkjs file need wp-init as a dependency.
					if (strpos($file, 'http') === 0) {
						$source = $file;
					} else {
						$source = $this->app->locateURL('js', $file.'.js');
					}

					wp_enqueue_script($file, $source, $required);
				}
			} else {
				foreach ($files as $file) {
					wp_enqueue_style($file, $this->app->locateURL('css', $file.'.css'));
				}
			}
		} catch (Exception $e) {
			// Handles output of the exception
			$this->app->caughtException($e);
		}

	}

	public function isAtkPanel($hook)
	{
		return $this->app->panelCtrl->isAtkPanel($hook);
	}

	public function getAtkPanel($hook)
	{
		return $this->app->panelCtrl->getAtkPanel($hook);
	}
}