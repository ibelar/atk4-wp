<?php

/**
 * atk form extension.
 *
 * Field error are accumulated in $formErrors array
 * When form is submitted, a call to $form->exitOnError() will display all errors to user
 * and aborted form submission.
 *
 */
class Form_WpForm extends Form
{

	public $formErrors = array();

	public function addMandatoryField( $type, $option=null, $caption=null, $attr=null )
	{
		return $this->makeFieldMandatory( $this->addField($type, $option, $caption, $attr) );
	}

	protected function makeFieldMandatory( $field )
	{
		$field->addHook( 'validate', [$this, 'validateMandatory']);
		return $field;
	}

	public function validateMandatory($field)
	{
		$value = trim($field->get());
		if($value==="" || is_null($value))
			$this->formErrors[$field->short_name] = _('This is a mandatory field');
	}


	public function exitOnError()
	{
		if ($this->hasError()){
			$this->_displayErrors();
		}
	}

	public function hasError()
	{
		return isset($this->formErrors) && !empty($this->formErrors);
	}

	public function displayFormErrors()
	{
		$this->_displayErrors();
	}

	private function _displayErrors()
	{
		$js=array();
		foreach ($this->formErrors as $field => $msg){
			$js[] = $this->js(true)->atk4_form('fieldError',$field,$msg);
		}
		$this->js(null,$js)->execute();
	}
}