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
 * Atk application for WordPress.
 */

class WpAtk extends App_Web
{

	//The  name of the plugin
	public $pluginName;

	//Whether initialized_layout is bypass or not.
	public $isLayoutNeedInitialise = true;

	//The enqueue controller.
	public $enqueueCtrl;
	//The panel controller.
	public $panelCtrl;
	//The Widget controller
	public $widgetCtrl;
	//The Metabox controller
	public $metaBoxCtrl;

	public $shortcodeCtrl;

	//the current wp view to output. ( Ex: admin panel, shortcode or metabox)
	public $panel;

	//default config files to read
	public $wpConfigFiles = [ 'config-wp', 'config-panel', 'config-enqueue', 'config-shortcode', 'config-widget', 'config-metabox' ];

	public $ajaxMode = false;

	//ATK43 init
	/** When page is determined, it's class instance is created and stored in here */
	public $page_object=null;

	/** Class which is used for static pages */
	public $page_class='Page';

	/** List of pages which are routed into namespace */
	public $namespace_routes=array();

	/** Object for a custom layout, introduced in 4.3 */
	public $layout=null;

	/** will contains the app html output when using wp shortcode */
	public $appHtmlBuffer;

	//the metabox being execute.
	public $metaBox;
	//the shortcode being execute
	public $shortcode;




	public function __construct($name, $configPath )
	{
		$this->pluginName = $name;
		$this->config_location = $configPath;
		parent::__construct( $name );
	}

	/**
	 * Initialise Wp Atk4 component
	 * Your plugin should override this class
	 *
	 * @throws AbstractObject
	 * @throws BaseException
	 */
	public function init()
	{
		parent::init();
		$this->setDsnConfig();
		$this->widgetCtrl       = $this->add( 'Wp_Controller_Widget', 'wpatk-wdg');
		$this->enqueueCtrl      = $this->add( 'Wp_Controller_Enqueue', 'wpatk-enq' );
		$this->panelCtrl        = $this->add( 'Wp_Controller_Panel', 'wpatk-pan' );
		$this->metaBoxCtrl      = $this->add( 'Wp_Controller_MetaBox', 'wpatk-mb');
		$this->shortcodeCtrl    = $this->add( 'Wp_Controller_Shortcode', 'wpatk-sc');


		$this->add( 'Wp_WpJui' );
		$this->template->trySet('action', $this->pluginName);

	}


	/**
	 * Header are sent by Wordpress.
	 * Leave empty.
	 */
	public function sendHeaders() { }

	/**
	 * @param $path
	 */
	public function setConfigLocation( $path)
	{
		$this->config_location = $path;
	}

	/**
	 * Override _beforeInit for Wordpress
	 * Setup our own Pathfinder class for wordpress.
	 */
	public function _beforeInit() {
		$this->pathfinder_class = 'WpPathfinder';
		// Loads all configuration files
		$this->config_files = array_merge( $this->config_files, $this->wpConfigFiles);

		$this->pm=$this->add($this->pagemanager_class, $this->pagemanager_options);
		$this->pm->parseRequestedURL();

		$this->readAllConfig();
		$this->add( $this->pathfinder_class );
	}

	/**
	 * Read config file and store it in $this->config. Use getConfig() to access
	 */

	public function readConfig( $file = 'config.php' ) {
		$orig_file = $file;

		if ( strpos( $file, '.php' ) != strlen( $file ) - 4 ) {
			$file .= '.php';
		}

		if ( strpos( $file, '/' ) === false ) {
			$file = $this->config_location . '/' . $file;
		}

		if ( file_exists( $file ) ) {
			// some tricky thing to make config be read in some cases it could not in simple way
			unset( $config );

			$config =& $this->config;
			$this->config_files_loaded[] = $file;
			include $file;

			unset( $config );

			return true;
		}

		return false;
	}

	/**
	 * Check for dsn configuration in config and if not set
	 * use Wordpress default.
	 */
	public function setDsnConfig()
	{
		if( ! $this->app->getConfig( 'dsn', null )){
			$this->app->setConfig('dsn', 'mysql://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME);
		}
	}

	/**
	 * Call by Wordpress plugin main file.
	 * Your Plugin Class file may overide this function
	 * in order to setup your own WP plugin init.
	 */
	public function wpInit()
	{
		$this->initializeSession(true);
		$this->setWpNonce();
	}

	/**
	 * Plugin Entry point
	 * Wordpress plugin file call this function in order to have
	 * atk4 work under Wordpress.
	 *
	 * Will load panel, metab box, widget and shortcode configuration file;
	 * Setup proper Wp action for each of them;
	 * Setup WP Ajax.
	 *
	 * @throws
	 */
	public function boot()
	{
		try {
			$this->panelCtrl->loadPanels();
			$this->widgetCtrl->loadWidgets();
			$this->metaBoxCtrl->loadMetaBoxes();
			$this->shortcodeCtrl->loadShortcodes();
			add_action( 'init', [ $this, 'wpInit']);
			//register ajax action for this plugin
			add_action( "wp_ajax_{$this->pluginName}", [$this, 'wpAjaxExecute'] );
			//enable Wp ajax front end action.
			add_action( "wp_ajax_nopriv_{$this->pluginName}", [$this, 'wpAjaxExecute'] );

		} catch ( Exception $e ) {
			$this->caughtException( $e );
		}

	}

	/*--------------------- OUTPUT ENTRY POINT -------------------------------*/

	/**
	 * Output Panel view in Wp.
	 *
	 */
	public function wpAdminExecute()
	{
		global $hook_suffix;
		$this->panel = $this->panelCtrl->getPanelUses( $hook_suffix );
		$this->main();
	}

	/**
	 * Output metabox view in Wp.
	 *
	 * Differnet metabox view may be output within the same admin page,
	 * it is necessary to reset the content after main is execute.
	 *
	 * @$post    Wp_Post //Contains the current post information
	 * @$param   Array   //Argument passed into the metabox, contains argument set in config file.
	 */
	public function wpMetaBoxExecute( WP_Post $post, array $param )
	{
		//set the view to output.
		$this->panel['class'] = $this->metaBoxCtrl->getMetaBoxByKey( $param['id'] )['uses'];//$this->metaBox;
		$this->panel['id']    = $param['id'];
		//Make our post info available for our view.
		$this->metaBox['post'] = $post;
		$this->metaBox['args'] = $param[ 'args' ];
		$this->isLayoutNeedInitialise = false;
		$this->metaBoxCtrl->metaDisplayCount ++;
		$this->main();
		$this->resetContent();
	}


	/**
	 * Output shortcode view in Wordpress.
	 * Shortcode may come from anywhere in Wp front.
	 * A shortcode view may be loaded x amount of time with other shortcode view that may also be loaded x amount of time.
	 * The shortcode controller will need to keep track of the number output for each view.
	 * @param $shortcode
	 * @param $args
	 *
	 * @return mixed
	 */
	public function wpShortcodeExecute( $shortcode, $args )
	{
		//Set app panel with proper shortcode class.
		$this->panel['class'] = $shortcode['uses'];
		$this->panel['id']    = $shortcode['key'];
		//Shortcode class will retreive this arg on init()
		$this->shortcode['args'] = $args;
		//Tell Shortcode controller how many time this is output.
		$this->shortcodeCtrl->increaseShortcodeInstance(  $shortcode['key'] );
		//This will set proper ajax action for this shortcode instance.
		$this->sticky_get_arguments['atkshortcode'] = $this->shortcodeCtrl->getShortcodeInstance(  $shortcode['key'] );
		$this->isLayoutNeedInitialise = false;
		//Shortcode are not echo and must return html
		$html = $this->getAppHtml();
		$this->resetContent();
		return $html;
	}

	/**
	 * Output ajax call in Wp.
	 * This is an overall catch ajax request for Wordpress admin and front.
	 */
	public function wpAjaxExecute()
	{
		$this->ajaxMode = true;
		$this->panel = $this->panelCtrl->getPanelUses( $_REQUEST['atkpanel'], false );
		if( isset( $_GET['atkshortcode'])){
			$this->stickyGet('atkshortcode');
		}
		check_ajax_referer( $this->pluginName );
		$this->main();
		die();
	}

	/**
	 * Reset this app and prepare for subsequent output.
	 * When multiple output of panel is required, for metabox or shortcode, this will reset
	 * the app in order to output only the necessary views and js chains.
	 *
	 */
	public function resetContent()
	{
		//remove the actual panel
		$this->removeElement($this->page_object->short_name);
		//clear document_ready tag content.
		$this->template->del('document_ready');
		// clear js chain.
		$this->js = null;
		$this->js = [];
		$this->clearAppHtml();

	}

	/**
	 * Implement Ajax security using Wp nonce by adding the nonce value to every ajax url.
	 */
	private function setWpNonce()
	{
		$this->sticky_get_arguments['_ajax_nonce'] = wp_create_nonce( $this->pluginName );
	}

	/**
	 * Generates URL for wordpress environment.
	 * Will generate admin-ajax url by default.
	 * Reason for this is not to change all atk view that need ajax call, like form, crud, grid etc.
	 * To generate a ajax url simply call:
	 *      Ex: $this->app->url() or using argument $this->app->url('', ['arg'=>'value'])
	 *
	 * If you need to generate an admin page link instead (ex: http://www.site.com/admin.php?arg=value )
	 * then you need to specify $page = 'admin' in your call.
	 *
	 *      Ex: $this->app->url('admin', ['arg'=>'value'])
	 *
	 * For external url simply use:
	 *
	 *      Ex: $this->app->url('http://www.site.com', ['arg'=>'value'])
	 *
	 * @param null $page
	 * @param  array $arguments [description]
	 *
	 * @return $this [type]            [description]
	 * @throws AbstractObject
	 * @throws BaseException
	 * @internal param $ [type] $page      [description]
	 */
	public function url( $page = null, $arguments = array() )
	{
		$url = $this->add( 'Wp_WpUrl' );
		unset( $this->elements[ $url->short_name ] );   // garbage collect URLs
		if ( strpos( $page, 'http://' ) === 0 || strpos( $page, 'https://' ) === 0 ) {
			$url->setURL( $page );
		} else {
			if( $page === 'admin'){
				$url->setBaseURL($url->wpAdminUrl);
			} else {
				//add ajax call argument.
				$arguments['action']   = $this->pluginName;
				$arguments['atkpanel'] = $this->panel['id'];
			}
			$url->setPage( $page );
		}

		return $url->setArguments( $arguments );
	}


	public function locatePublicUrl( $file )
	{
		try {
			//remove unnecessary / for public url
			return preg_replace('#/+#','/', $this->locateUrl('public', $file));
		} catch (Exception $e) {
			$this->caughtException( $e );
		}
	}

	public function getPanelSlug()
	{
		return $this->panelCtrl->getPanelSlugByKey( $this->panel['id']);
	}

	/**
	 * Added a check for this WP namespace.
	 * if our current namespace is found, then simply return the class.
	 * This allow us to use our own namespace when using model ref.
	 *      ex: $m->hasOne('nameSpace\Model\ClassName', 'model_id')
	 * if namespace is not found, will proceed normally.
	 *
	 * Example: normalizeClassName('User','Model') == 'Model_User';
	 *
	 * @param string|object $name   Name of class or object
	 * @param string        $prefix Optional prefix for class name
	 *
	 * @return string|object Full, normalized class name or received object
	 *
	 * todo add namespace in a config file and check for different namespace there.
	 * todo will allow to use addon in their own namespace by configuring composer.json accordingly.
	 * todo otherwise, when using addon autocomplete field type in form will not work.
	 */

	public function normalizeClassName($name, $prefix = null)
	{

		$app_namespace = (new \ReflectionObject($this))->getNamespaceName();
		//check if class need to be load as is when using our own namespace.
		if ( strpos( $name, $app_namespace ) === false ){
			$name = parent::normalizeClassName( $name, $prefix );
		}
		return $name;
	}

	public function isAjaxMode()
	{
		return $this->ajaxMode;
	}

	public function defaultTemplate()
	{
		return ['wp-html'];
	}


	/** Renders all objects inside applications and echo all output to the browser */
	public function render()
	{
		$this->hook('pre-js-collection');
		if(isset($this->app->jquery) && $this->app->jquery)$this->app->jquery->getJS($this);

		if(!($this->template)){
			throw new BaseException("You should specify template for API object");
		}

		$this->hook('pre-render-output');
		//check if we need to return html for shortcode instead of regular echo.
		if ( $this->hook('sc_render')){
			return;
		}
		//remove shortcode layout prior to echo template output.
		//$this->template->del('Shortcode');
		echo $this->template->render();
		$this->hook('post-render-output');
	}

	public function outputDebug( $msg, $shift=0 )
	{
		if( $msg instanceof DB ){
			$this->js(true)->univ()->dialogOK('SQL Debug', $shift);
			return;
		}
		if($this->hook('output-debug',array($msg,$shift)))return true;
		echo "<font color=blue>",$msg,"</font><br>";
	}

	/**
	 * Will render all objects within the app and collect
	 * all Html in appHtmlBuffer.
	 *
	 * Shortcode are using getAppHtml because Wordpress shortcode need
	 * to return html value instead of sending it via echo.
	 *
	 * @return mixed
	 * @throws BaseException
	 * @throws Exception_StopRender
	 */
	public function getAppHtml()
	{
		$this->addHook('sc_render', [$this, 'setAppHtmlBuffer']);
		$this->main();
		return $this->appHtmlBuffer;
	}

	public function clearAppHtml()
	{
		$this->appHtmlBuffer = null;
	}

	/**
	 * Output the app template to buffer.
	 * Need to remove Layout tag prior to output template.
	 */
	public function setAppHtmlBuffer()
	{
		//$this->template->del('Layout');
		$this->appHtmlBuffer = $this->template->render();
	}

	public function initLayout( )
	{
		if( $this->isLayoutNeedInitialise ){
			parent::initLayout();
		}
		$this->addLayout('Content');
	}


	/** Default handling of Wp Content page. */
	public function layout_Content()
	{
		$layout = $this->layout ?: $this;
		$this->page_object = $layout->add($this->panel['class'], [ 'name' => $this->panel['id'], 'id' => $this->panel['id']]);
	}

	public function getWpPageUrl()
	{
		global $post;

		$url = '';
		if ( is_home() ){
			$url = site_url();
		} else {
			$url = get_permalink( $post->ID );
		}

		return $url;
	}

	/**
	 * It's ok to clone this app.
	 * Widget for example will use a clone to render html views
	 *
	 * Without cloning, view define for widget will also be output in panel.
	 * This way, widget use his own atk instance to generate it's own view.
	 */
	public function __clone()
	{
		$this->enqueueCtrl = null;
		$this->panelCtrl   = null;
	}

	/*-------------------- THEME SECTION ---------------------------------*/

	/** can be use as a theme page output  */

	/**
	 * Parse request and check if we need to preset a panel
	 * When Wp running in front end, we need to parse request
	 * and ckeck if the pagename correspond to a specific panel to be use
	 * via the panel page_slug.
	 * If a panel is required, it will then call preSetPanel
	 * preSetPanel is called specially to load proper atk js and css file if needed.
	 *
	 * @param $query
	 */
	public function parseRequest( $query )
	{

		$panels = $this->panelCtrl->getPanels();
		//let see if this page required one of our panel.
		$pagename = $query->query_vars['pagename'] ;

		foreach( $panels as $key => $panel){
			if ( $pagename === $panel['page_slug']){
				$this->panelCtrl->preSetPanel( $panel );
			}
		}

	}


	/**
	 * Usually call from a theme template page where pagename is already defined.
	 * Will setup app with proper panel to be display.
	 *
	 * Ex use from a theme template page
	 *
	 *   $pluginName->outputPanel( $pagename ); // $pagename is the actual page request
	 *
	 * if a panel is registered with that pagename (page_slug) value, it will be load
	 * prior to echo ouptut
	 *
	 * @param $page
	 */
	public function outputPanel( $page )
	{
		$this->panel = $this->panelCtrl->getFrontPanelUses( $page );
		$this->main();
	}

	/*-------------------- END THEME SECTION ---------------------------------*/

}