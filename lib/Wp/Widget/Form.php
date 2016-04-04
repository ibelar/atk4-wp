<?php

/**
 *  Basic Form use in Wp Widgets
 *
 * Mainly modifiying the addField function in order to adapt to Wordpress widget.
 * Field in widget form need to have seperate name and id value, as opposed to atk where
 * field name and id are the same.
 *
 * Widget field also required wordpress css class and addField will take care of this.
 *
 */
class Wp_Widget_Form extends Form_Basic
{
	public $fieldCSSDefaultClass = 'widefat';
	public $fieldCssSpecialClasses = [ 'number' => 'tiny-text', 'checkbox' => 'checkbox', 'hidden' => ''];
	public $widgetFieldType  = ['line', 'checkbox', 'dropdown', 'number', 'valuelist','date'];

	public function init()
	{
		parent::init();

		//no need for js widget.
		$this->js_widget = null;


	}

	/**
	 * Add Field to form and set Wordpress css class to it.
	 *
	 * Note: Usually, widget will display the first field value in widget bar.
	 * This is done (guessing here) via the id attribute value of the input field.
	 * Since atk field id is the same as the field name, wordpress is unable to calculated the
	 * proper value. To correct this, we would need the field input attribute id and name value to be different
	 * as in Wordpress does. To do this, we need to modified the Form_Field class by adding a $field->id property,
	 * make this property equal to $name for backward compatibility in init $field->id = $field->name and change
	 * the getInput function by setting the field id = $field->id instead of $field->name. Finally, in our Widget form function
	 * setup the id porperty using $fied->id = $this->get_field_id( $field_name ) function.
	 *
	 *
	 * @param $type
	 * @param null $options
	 * @param null $caption
	 * @param null $attr
	 *
	 * @return mixed
	 * @throws BaseException
	 */
	public function addField( $type, $options = null, $caption = null, $attr = null )
	{
		//return parent::addField( $type, $options, $caption, $attr )->addClass( $this->fieldClassName );

		$insert_into = $this->layout ?: $this;

		if (is_object($type) && $type instanceof AbstractView && !($type instanceof Form_Field)) {

			// using callback on a sub-view
			$insert_into = $type;
			list(,$type,$options,$caption,$attr)=func_get_args();

		}

		if ($options === null) {
			$options = $type;
			$type = 'Line';
		}

		if (is_array($options)) {
			$name = isset($options["name"]) ? $options["name"] : null;
		} else {
			$name = $options; // backward compatibility
		}
		$name = preg_replace('|[^a-z0-9-_]|i', '_', $name);

		if ($caption === null) {
			$caption = ucwords(str_replace('_', ' ', $name));
		}

		/* normalzie name and put name back in options array */
		$name = $this->api->normalizeName($name);
		if (is_array($options)){
			$options["name"] = $name;
		} else {
			$options = array('name' => $name);
		}

		switch (strtolower($type)) {
			case 'dropdown':     $class = 'DropDown';     break;
			case 'checkboxlist': $class = 'CheckboxList'; break;
			case 'hidden':       $class = 'Hidden';       break;
			case 'text':         $class = 'Text';         break;
			case 'line':         $class = 'Line';         break;
			case 'upload':       $class = 'Upload';       break;
			case 'radio':        $class = 'Radio';        break;
			case 'checkbox':     $class = 'Checkbox';     break;
			case 'password':     $class = 'Password';     break;
			case 'timepickr':    $class = 'TimePicker';   break;
			default:             $class = $type;
		}
		if ( in_array( $type, $this->widgetFieldType )) {
			$class = $this->api->normalizeClassName($class, 'Wp_Widget_Field');
		} else {
			//revert to default class type
			$class = $this->api->normalizeClassName($class, 'Form_Field');
		}


		if ($insert_into === $this) {
			$template=$this->template->cloneRegion('form_line');
			$field = $this->add($class, $options, null, $template);
		} else {
			if ($insert_into->template->hasTag($name)) {
				$template=$this->template->cloneRegion('field_input');
				$options['show_input_only']=true;
				$field = $insert_into->add($class, $options, $name);
			} else {
				$template=$this->template->cloneRegion('form_line');
				$field = $insert_into->add($class, $options, null, $template);
			}

			// Keep Reference, for $form->getElement().
			$this->elements[$options['name']]=$field;
		}

		if( key_exists( $type, $this->fieldCssSpecialClasses )){
			$field->addClass( $this->fieldCssSpecialClasses[$type]);
		} else {
			$field->addClass( $this->fieldCSSDefaultClass );
		}


		$field->setCaption($caption);
		$field->setForm($this);
		$field->template->trySet('field_type', strtolower($type));

		if($attr) {
			if($this->app->compat) {
				$field->setAttr($attr);
			}else{
				throw $this->exception('4th argument to addField is obsolete');
			}
		}

		return $field;

	}

	protected function getChunks(){
		// commonly replaceable chunks
		$this->grabTemplateChunk('form_comment');
		$this->grabTemplateChunk('form_separator');
		$this->grabTemplateChunk('form_line');      // template for form line, must contain field_caption,field_input,field_error
		if($this->template->is_set('hidden_form_line'))
			$this->grabTemplateChunk('hidden_form_line');
		$this->grabTemplateChunk('field_error');    // template for error code, must contain field_error_str
		$this->grabTemplateChunk('field_mandatory');// template for marking mandatory fields

		// other grabbing will be done by field themselves as you will add them
		// to the form. They will try to look into this template, and if you
		// don't have apropriate templates for them, they will use default ones.
		$this->template_chunks['form']=$this->template;
		$this->template_chunks['form']->del('Content');
		$this->template_chunks['form']->del('form_buttons');
		$this->template_chunks['form']->trySet('form_name',$this->name.'_form');
		//$this->template_chunks['form']->set('form_action',$this->api->url(null,array('submit'=>$this->name)));

		return $this;
	}

	/*public function getHTML( $destroy = true, $execute_js = true ) {
		return parent::getHTML( $destroy, $execute_js ); // TODO: Change the autogenerated stub
	}*/

	public function defaultTemplate( )
	{
		return ['widget/form'];
	}
}