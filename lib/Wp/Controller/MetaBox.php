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
 * This controller is responsible for registering metabox.
 *
 */

class Wp_Controller_MetaBox extends AbstractController
{
	public $metaBoxes = [];
	public $metaDisplayCount = 0;


	public function init()
	{
		parent::init();
	}

	/**
	 * Load metaBoxes define in our config file.
	 */
	public function loadMetaBoxes()
	{
		$metaboxes = $this->app->getConfig('metabox', null);
		if (isset($metaboxes)) {
			foreach ($metaboxes as $key=>$metabox) {
				$this->registerMetaBox($key, $metabox);
			}
		}
	}

	public function getMetaBoxByKey($key)
	{
		return $this->metaBoxes[$key];
	}

	public function registerMetaBox($key, $metabox)
	{
		//create metaBoxes using closure function.
		add_action('add_meta_boxes', function() use ($key, $metabox) {
			$metabox['key'] = $key;

			//Add atk4 js and css files using our key as panel hook
			$this->app->enqueueCtrl->enqueueAdminFiles($key);
			$args = (isset($metabox['args']))? $metabox['args'] : null;

			add_meta_box($key , $metabox['title'], [$this->app, 'wpMetaBoxExecute'] , $metabox['type'], $metabox['context'], $metabox['priority'], $args);

		});
		//register this metabox in panel ctrl for ajax calls.
		$this->app->panelCtrl->setPanels($key, $metabox);
		$this->app->panelCtrl->registerPanelHook($key , $key);
		//Add save post action
		add_action('save_post_'.$metabox['type'], [$this, 'savePostType'], 10, 3 );
		//Add it to our list of metaBox.
		$this->metaBoxes[$key] = $metabox;

	}

	/**
	 * Save post is fire early in Wp and get redirect after post has been saved.
	 * Redirection is done prior of adding metaBox to this app.
	 *
	 * In order to delegate the saving of metaBox field to the metaBox class, we
	 * need to rebuild them within our app and call the savePost function.
	 */
	public function savePostType($postId, WP_Post $post, $isUpdating)
	{
		//Add new post will trigger the save post hook and isUpdating will be false
		// We do not need to add our metabox when adding new post only when updating them.
		if ($isUpdating) {
			foreach ($this->metaBoxes as $key=>$metaBox) {
				$box = $this->app->add($metaBox['uses'], ['name'=>$key, 'id'=>$key]);
				$box->savePost($postId);
			}
		}

	}
}