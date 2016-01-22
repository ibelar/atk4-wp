<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-11
 * Time: 9:41 AM
 */
class WpAtk extends App_Web
{

	public $pluginName;


	//The enqueue controller.
	public $enqueueCtrl;
	//The panel controller.
	public $panelCtrl;

	//the panel to load into wp .
	public $panel;

	//config files
	public $wpConfigFiles = [ 'config-panel', 'config-enqueue', 'config-shortcode'];

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
		//$this->dbConnect();
		$this->enqueueCtrl = $this->add( 'Wp_Controller_Enqueue', 'wpatk-enq' );
		$this->panelCtrl   = $this->add( 'Wp_Controller_Panel', 'wpatk-pan' );
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
	 * Call by Wordpress plugin main file.
	 * Your Plugin Class file may overide this function
	 * in order to setup your own WP plugin init.
	 */
	public function wpInit()
	{
		$this->initializeSession(true);
	}

	/**
	 * Plugin Entry point
	 * Wordpress plugin file call this function in order to have
	 * atk4 work under Wordpress.
	 *
	 * Will load panel and shortcode configuration file;
	 * Setup proper action and filter for them;
	 * Setup WP Ajax
	 *
	 * Note: Loading panel and shortcode and adding wp_ajax action should be done
	 * first in boot function. Trying to put this in if is_admin() statement will cause
	 * ajax error.
	 *
	 * @throws
	 */
	public function boot()
	{
		try {
			$this->panelCtrl->loadPanels();
			$this->loadShortcodes();
			//register ajax action for this plugin
			add_action( "wp_ajax_{$this->pluginName}", [$this, 'wpAjaxExecute'] );
			//enable Wp ajax front end action.
			add_action( "wp_ajax_nopriv_{$this->pluginName}", [$this, 'wpAjaxExecute'] );

			if ( is_admin() ) {

			} else {
				//check if Wp page required an atk4 panel
				//add_action('parse_request', [$this, 'parseRequest']);

			}


		} catch ( Exception $e ) {
			// Handles output of the exception
			$this->caughtException( $e );
		}

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

	/**
	 * Call to $api->main() from register Wordpress action, like Panel.
	 *
	 * Defining an admin panel in config will load the panel and register this function to be
	 * run when the panel needs to be display.
	 */
	public function wpAdminExecute()
	{
		global $hook_suffix;
		$this->panel = $this->panelCtrl->getPanelUses( $hook_suffix );
		$this->main();
	}

	/**
	 * Call to $api->main() from register wordpress ajax action.
	 * This is an overall catch ajax request for wordpress.
	 */
	public function wpAjaxExecute()
	{
		$this->ajaxMode = true;
		$this->panel = $this->panelCtrl->getPanelUses( $_REQUEST['atkpanel'], false );
		$this->main();
		die();
	}

	/**
	 * Call to $api->main() from register wordpress ajax action.
	 * This is an overall catch ajax request for wordpress.
	 */
	/*public function wpAdminAjaxExecute()
	{
		$this->ajaxMode = true;
		$this->panel = $this->panelCtrl->getPanelUses( $_REQUEST['atkpanel'], false );
		$this->main();
		die();
	}


	public function wpFrontAjaxExecute()
	{
		$this->ajaxMode = true;
		$this->panel = $this->panelCtrl->getPanelUses( $_REQUEST['atkpanel'], false );
		$this->main();
		die();
	}*/


	/**
	 * Load shortcode setup in config file and register them within Wordpress.
	 *
	 * @throws App_CLI
	 * @throws BaseException
	 */
	public function loadShortcodes()
	{
		if ( $shortcodes = $this->getConfig( 'shortcode', null ) ) {
			foreach ( $shortcodes as $key => $shortcode ) {
				$this->registerShortcode( $key, $shortcode );
			}
		}
	}

	/**
	 * Register shortcode within wordpress and setup closure function to call
	 * when wordpress need to display a shortcode.
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
	public function registerShortcode( $key, $shortcode ) {
		$self = $this;
		add_shortcode( $shortcode['name'], function ( $args ) use ( $key, $shortcode, $self ) {
			$sc = $self->add( $shortcode['uses'], [ 'id' => $key, 'name'=> $shortcode['name'], 'needAtkJs' => $shortcode['atkjs'], 'args' => $args] );
			if ( isset($shortcode['js'])){
				$this->enqueueCtrl->enqueueFiles( $shortcode['js'], 'js', ['start-atk4']);
			}
			if ( isset($shortcode['css'])){
				$this->enqueueCtrl->enqueueFiles( $shortcode['css'], 'css');
			}

			$scHtml = $self->getAppHtml();
			$self->clearAppHtml();
			$self->removeElement($sc->short_name);

			return $scHtml;
		});
		//add this shortcode to our panel list.
		//This will allow to get ajax working.
		$this->app->panelCtrl->setPanels( $key, $shortcode );

	}


	/*public function renderShortcode ( $args )
	{
		$this->add( $shortcode['uses'], [ 'id' => $key, 'name'=> $shortcode['name'], 'needAtkJs' => $shortcode['atkjs'], 'args' => $args] );
		return $this->getHtml();
	}*/

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
		$this->template->del('Shortcode');
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
		$this->recursiveRender();
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
		$this->template->del('Layout');
		$this->appHtmlBuffer = $this->template->render();
	}

	public function initLayout()
	{
		parent::initLayout();
		$this->addLayout('Content');
	}


	/** Default handling of Wp Content page. */
	public function layout_Content()
	{
		$layout = $this->layout ?: $this;
		$this->page_object = $layout->add($this->panel['class'], [ 'name' => $this->panel['id'], 'id' => $this->panel['id']]);
		/*if ( is_admin() ){
			$this->page_object = $layout->add($this->panel['class'], [ 'name' => $this->panel['id'], 'id' => $this->panel['id']]);
		} else {
			$this->page_object = $layout->add($this->panel['class'], [ 'name' => $this->panel['id'], 'id' => $this->panel['id']]);
		}*/
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

}