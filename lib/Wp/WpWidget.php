<?php

/**
 * WpAtk main widget class
 * Extends Wordpress Widget class.
 * Build the necessary atk view and form to use with a widget.
 *
 * Your plugin should extends this class when creating widget.
 * Even if this class is a Wordpress Widget class, it has lots of similarity with atk framework.
 *          $widget->addWidgetDisplay( $displayView ); //displayView being an atk abstractView
 *                                                       You can pass a class name or a view instance build directly using the atk instance
 *                                                        $v =  $widget->atkInstance->add('View');
 *                                                        $widget->addWidgetDisplay( $v );
 *          $widget->addWidgetForm ( $form );          // Form being an atk form view.
 *                                                        Same as View, you can pass in a class name or a form instance build with the atk instance
 *          $widget->onDisplay( $callback )            // Run callback function when widget is about to be display.
 *                                                        the function will receive the atk view and form instance has parameter.
 *                                                        This is a good place to run db query and set your atk view just before diplay.
 *
 * Displaying the widget is done via the widgetDisplay view build from an atk4 abstract view.
 *          - you may set the widgetDisplay via the addWidgetDisplay() function in your child class or via
 *              the display uses option in your config.
 * When the widget function is call by wordpress, an html representation of the widgetDisplay view will be output.
 *          - your widget child class will also have a chance to update the view using an action hook: $widget->onDisplay( $callback ).
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

	public $instanceDefaults = [];


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
	 * @param $id           //The id set in config-widget
	 * @param $widget       //The widget configuration as set in config-widget
	 * @param $atkInstance  //A clone of WpAtk application
	 */
	public function beforeInit( $id, $widget, $atkInstance )
	{
		//make sure our id_base is unique
		$this->id_base      = $atkInstance->name . '_wdg_' . $id;
		//Widget option_name in Option table that will hold the widget instance field value.
		$this->option_name = 'widget_' . $this->id_base;
		// Our widget definition
		$this->atkWidget = $widget;
		//Add the id value to our widget definition.
		$this->atkWidget['id'] = $id;
		// The atk instance to build view and form.
		$this->setAtkInstance( $atkInstance );
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

		//setup widget display
		if( isset ($this->atkWidget['display']['uses']) && !empty($this->atkWidget['display']['uses']) ){
			$this->addWidgetDisplay( $this->atkWidget['display']['uses']);
		}
		if(  isset ($this->atkWidget['display']['title']) && !empty($this->atkWidget['display']['title'])  ){
			$this->setWidgetDisplayTitle( $this->atkWidget['display']['title'] );
		}

		//setup widget form and field
		if(  isset ($this->atkWidget['form']['uses']) && !empty($this->atkWidget['form']['uses']) ){
			$this->addWidgetForm( $this->atkWidget['form']['uses']);
		}
		if(  isset ($this->atkWidget['fields']) && !empty($this->atkWidget['fields'] )){
			if( !isset( $this->widgetForm )){
				$this->addWidgetForm( 'Wp_Widget_Form');
			}
			foreach( $this->atkWidget['fields'] as $name => $field ){
				$f = $this->widgetForm->addField( $field['type'], $name );
				if( isset ($field['caption'])){
					$f->setCaption( $field['caption'] );
				}
				if (isset ($field['list'])){
					$f->setValueList( $field['list'] );
				}
				//$fieldDefault = isset( $field['default'] )? $field['default'] : '';
				$this->instanceDefaults[$name] = isset( $field['default'] )? $field['default'] : '';
			}
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

	public function setInstanceDefaults( Array $defaults )
	{
		$this->instanceDefaults = $defaults;
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
		$this->addHook( 'onDisplay', $callback );
	}


	public function addWidgetForm( $className )
	{
		$this->widgetForm = $this->atkInstance->add( $className, 'wdg-form_' . $this->atkWidget['id'] );
		if ( ! $this->widgetForm instanceof Wp_Widget_Form ) {
			throw $this->atkInstance->exception(_('Form class need to be child of Wp_Widget_Form'));
		}
		return $this->widgetForm;
	}

	/**
	 * Add form hook to our widget.
	 * This hook will be run prior to display our widget form.
	 * It will allow to setup your form prior to displaying it.
	 *
	 * Ex: $this->onForm( [$this, 'beforeForm']);
	 *
	 * This action pass two arguments to the callback function: the atk form and the instance field.
	 *
	 * @param $callback
	 */
	public function onForm( $callback = null )
	{
		$this->addHook( 'onForm', $callback );
	}


	/**
	 * Display widget in wordpress.
	 * Call by WP when the widget need to be display.
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{

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

	/**
	 * Echo form input in widget admin area of WP
	 *
	 * Check if an atk4 form is defined and echo form element.
	 *
	 * @param array $instance       //the form instance
	 * @return string|void
	 */
	public function form( $instance )
	{
		$instance = wp_parse_args( (array) $instance, $this->instanceDefaults );
		$this->hook( 'onForm', [$this->widgetForm, $instance] );
		if( isset( $this->widgetForm )){
			foreach($this->widgetForm->elements as $x => $field){
				if($field instanceof \Form_Field){
					$field->name = $this->get_field_name( $field->short_name );
					$field->id = $this->get_field_id( $field->short_name );
					$field->set( $instance[ $field->short_name ]);
				}
			}
			$html = $this->widgetForm->getHTML( false, false );
			//IMPORTANT: Ounce we have our html, we need to remove previous content in our template form
			//Otherwise, the output gets append in render() function and form line will be
			//display twice.
			$this->widgetForm->template->del('Content');
			echo $html;
		} else {
			parent::form( $instance );
		}
	}


	/**
	 * Update the widget instance.
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return mixed
	 */
	public function update( $new_instance, $old_instance )
	{
		//$instance = $old_instance;
		//Compare key using the default one
		//If using a checkbox, key will not be present in new_instance but will be in instanceDefaults.
		foreach( $this->instanceDefaults as $key => $data ){
			$instance[ $key ] = strip_tags( $new_instance[ $key ] );
		}
		return $instance;
	}


	/**
	 * Call a hook spot.
	 * @param $spot
	 * @param null $args
	 *
	 * @return mixed|null
	 */
	private function hook( $spot, $args = null )
	{
		$result = null;
		if (isset ($this->hooks[$spot] )) {
			$hook = $this->hooks[$spot];
			$result = call_user_func_array(	$hook, $args);
		}
		return $result;
	}

	/**
	 * Ad a hook spot.
	 * @param $hook
	 * @param $callback
	 */
	private function addHook( $hook, $callback )
	{
		if ( method_exists( $callback[0], $callback[1] ) && is_callable( [ $callback[0], $callback[1] ])){
			$this->hooks[ $hook ] = $callback;
		}
	}
}