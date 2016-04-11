<?php

/**
 * Created by abelair.
 * Date: 2016-04-05
 * Time: 9:20 AM
 */
class Wp_Controller_MetaBox extends AbstractController
{
	//the atk instance
	protected $metaBoxAtkApp;

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
		if( isset($metaboxes)){
			foreach( $metaboxes as $key => $metabox ){
				$this->registerMetaBox( $key, $metabox );
			}
		}
	}

	public function getMetaBoxByKey( $key )
	{
		return $this->metaBoxes[ $key ];
	}

	public function registerMetaBox( $key, $metabox )
	{
		//create metaBoxes using closure function.
		add_action( 'add_meta_boxes', function() use ($key, $metabox ){
			$metabox['key'] = $key;

			$this->app->metaBox['class'] = $metabox['uses'];
			$this->app->metaBox['id']    = $metabox['key'];

			//Add atk4 js and css files using our key as panel hook
			$this->app->enqueueCtrl->enqueueAdminFiles( $key );
			$args = (isset($metabox['args']))? $metabox['args'] : null;

			add_meta_box( $key , $metabox['title'], [$this->app, 'wpMetaBoxExecute'] , $metabox['type'], $metabox['context'], $metabox['priority'], $args );

		});
		//register this metabox in panel ctrl for ajax calls.
		$this->app->panelCtrl->setPanels( $key, $metabox );
		$this->app->panelCtrl->registerPanelHook( $key , $key );
		//Add save post action
		add_action('save_post_'.$metabox['type'], [ $this, 'savePost']);
		//Add it to our list of metaBox.
		$this->metaBoxes[ $key ] = $metabox;

	}

	/**
	 * Save post is fire early in Wp and get redirect after post has been saved.
	 * Redirection is done prior of adding metaBox to this app.
	 *
	 * In order to delegate the saving of metaBox field to the metaBox class, we
	 * need to rebuild them within our app and call the savePost function.
	 */
	public function savePost( $postId )
	{
		foreach ( $this->metaBoxes as $key => $metaBox ){
			$box = $this->app->add( $metaBox['uses'], [ 'name' => $key, 'id' => $key ] );
			$box->savePost( $postId );
		}
	}

	/**
	 * Will return a singleton copy of WpAtk app use for metabox.
	 */
	/*private function getMetaBoxAtkApp()
	{
		if( !isset ($this->MetaBoxAtkApp) ) {
			$this->MetaBoxAtkApp = clone $this->app;
		}
		return $this->MetaBoxAtkApp;
	}*/
}