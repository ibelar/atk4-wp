<?php

/**
 * WpAtk main widget class
 * Extends Wordpress Widget class.
 *
 * Displaying the widget is done via the widgetDisplay view that hold an instance of an atk4 abstract view.
 *          - you may set the widgetDisplay via the addWidgetDisplay() function in your child class or via
 *              the display uses option in your config.
 * when the widget function is call by wordpress, an html representation of the widgetDisplay view will be output.
 *          - your widget child class will also have a chance to update the view using an action hook ($this->onDisplay( $callback )).
 */
class Wp_WpWidget extends WP_Widget
{
	//keep track of widget object instantiate
	//public static $count = 0;

	//The wpatk instance
	public $atkInstance;
	// the widget configuration and option
	public $atkWidget;

	// the wpatk view need for displayin this widget.
	public $widgetDisplay = null;
	public $widgetDisplayTitle = '';

	public $widgetForm = null;

	public $hooks = [];


	/**
	 * Wp_WpWidget constructor.
	 * Using static count var to generate different widget id.
	 */
	public function __construct( )
	{
		parent::__construct( 'atk4', '', [] );
	}

	/**
	 * Pre initialisation of our widget.
	 * Call from the Widget Controller on widget registration in WP.
	 * Call directly after widget creation
	 * @param $id           The id set in config-widget
	 * @param $widget       The widget configuration as set in config-widget
	 * @param $instance     A clone of WpAtk application
	 */
	public function beforeInit( $id, $widget, $instance )
	{
		//make sure our id_base is unique
		$this->id_base      = $instance->name . '_wdg_' . $id;
		//Widget option_name in Option table that will hold the widget option value.
		$this->option_name = 'widget_' . $this->id_base;
		// Our widget definition
		$this->atkWidget = $widget;
		//Add the id value to our widget definition.
		$this->atkWidget['id'] = $id;
		// The atk instance to build view and form.
		$this->setAtkInstance( $instance );
		$this->init();
	}

	/**
	 * Basic initialisation of our widget.
	 *
	 */
	public function init()
	{
		//Admin Title has it appear in admin WP widget.
		$this->name             = $this->atkWidget['title'];
		$this->widget_options   = wp_parse_args( $this->atkWidget['widget_ops'], array('classname' => $this->option_name) );
		$this->control_options  = wp_parse_args( $this->atkWidget['widget_control_ops'], array('id_base' => $this->id_base) );
		if ( isset ($this->atkWidget['display']['uses']) && !empty($this->atkWidget['display']['uses']) ){
			$this->addWidgetDisplay( $this->atkWidget['display']['uses']);
		}
		if (  isset ($this->atkWidget['display']['title']) && !empty($this->atkWidget['display']['title'])  ){
			$this->setWidgetDisplayTitle( $this->atkWidget['display']['title'] );
		}
	}

	/**
	 * Set the atkInstance managing this widget.
	 * @param $atkInstance
	 */
	public function setAtkInstance( $atkInstance )
	{
		$this->atkInstance = $atkInstance;
	}

	/**
	 * Add the atk view attached to this widget.
	 * This view will be echo when this widget need to be display in WP.
	 * @param $className
	 * @param null $displayTitle
	 *
	 * @return null
	 */
	public function addWidgetDisplay( $className, $displayTitle = null )
	{
		$this->widgetDisplay = $this->atkInstance->add( $className, 'wdg-view_' . $this->atkWidget['id'] );
		if( isset ($displayTitle )){
			$this->setWidgetDisplayTitle( $displayTitle );
		}
		return $this->widgetDisplay;
	}

	/**
	 * Set the Widget Title when display in WP
	 * Note this is the title to be display in WP Front end
	 * and not the one display in admin area.
	 *
	 * @param $displayTitle
	 */
	public function setWidgetDisplayTitle( $displayTitle )
	{
		$this->widgetDisplayTitle = $displayTitle;
	}


	/**
	 * Add display hook to our widget.
	 * This hook will be run prior to display our widget.
	 * It will allow to setup your atk view prior to displaying it.
	 *
	 * Ex: $this->onDisplay( [$this, 'beforeDisplayWidget']);
	 *
	 * This action pass two arguments to the callback function: the atk view to be display and the instance field.
	 *
	 * @param $callback
	 */
	public function onDisplay( $callback = null )
	{
		if ( method_exists( $callback[0], $callback[1] ) && is_callable( [ $callback[0], $callback[1] ])){
			//add action unique to our widget class
			//add_action($this->id_base . '_before_atk_widget_display', $callback, 10, 2 );
			$this->hooks['onDisplay'] = $callback;
		}
	}


	public function addWidgetForm( $className )
	{
		$this->widgetForm = $this->atkInstance->add( $className/*, 'wdg-form_' . $this->atkWidget['id']*/);
		if ( ! $this->widgetForm instanceof Wp_Widget_Form ) {
			throw $this->atkInstance->exception(_('Form class need to be child of Wp_Widget_Form'));
		}
		return $this->widgetForm;
	}




	/**
	 * Display widget in wordpress.
	 * Call by WP when the widget need to be display.
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {

		$this->hook( 'onDisplay', [$this->widgetDisplay, $instance] );
		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $this->widgetDisplayTitle );
		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

		if( isset( $this->widgetDisplay)) {
			echo $this->widgetDisplay->getHtml();
		} else {
			echo '<p> The widget class display is not set; Class: '. get_class($this) .'</p>';
		}
		echo $args['after_widget'];
	}

	public function hook( $spot, $args = null )
	{
		$result = null;
		if (isset ($this->hooks[$spot] )) {
			$hook = $this->hooks[$spot];
			$result = call_user_func_array(	$hook, $args);
		}
		return $result;
	}
}