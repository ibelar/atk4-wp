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
 * This controller is responsible for registering dashboard widget.
 *
 */

class Wp_Controller_Dashboard extends AbstractController
{

	public $dashboards = [];

	public function init()
	{
		parent::init();
	}

	/**
	 * Load metaBoxes define in our config file.
	 */
	public function loadDashboards()
	{
		$dashboards = $this->app->getConfig('dashboard', null);
		if (isset($dashboards)) {
			foreach ($dashboards as $key => $dashboard) {
				$this->registerDashboard($key, $dashboard);
			}
		}
	}


	public function registerDashboard($key, $dashboard)
	{
		//create metaBoxes using closure function.
		add_action('wp_dashboard_setup', function() use ($key, $dashboard) {
			//Add atk4 js and css files using our key as panel hook
			$this->app->enqueueCtrl->enqueueAdminFiles($key);

			if (isset($dashboard['form']['uses'])) {
				$formCallback = function() use ($key, $dashboard) {
					$this->app->wpDashboardExecute($key, $dashboard, true);
				};
			} else {
				$formCallback = null;
			}


			wp_add_dashboard_widget($key ,
									$dashboard['title'],
									function() use ($key, $dashboard){
										$this->app->wpDashboardExecute($key, $dashboard);
									},
									$formCallback
			);

		});
		//register this metabox in panel ctrl for ajax calls.
		$this->app->panelCtrl->setPanels($key, $dashboard);
		$this->app->panelCtrl->registerPanelHook($key , $key);
		//Add save post action
		//add_action('save_post_'.$metabox['type'], [$this, 'savePostType'], 10, 3 );
		//Add it to our list of metaBox.
		$this->dashboards[$key] = $dashboard;
	}


	public function dashboardCallback()
	{
		$t = 't';
	}


}