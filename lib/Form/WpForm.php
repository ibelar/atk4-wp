<?php

/**
 * Created by abelair.
 * Date: 2015-09-22
 * Time: 8:39 AM
 */
class Form_WpForm extends Form
{

	public $formErrors = array();

	public function validateMandatory($field)
	{
		$value = trim($field->get());
		if($value==="" || is_null($value))
			$this->formErrors[$field->short_name] = _('This is a mandatory field');
	}

	public function hasError()
	{
		return isset($this->formErrors) && !empty($this->formErrors);
	}

	public function displayFormErrors()
	{
		$this->_displayErrors();
	}

	private function _displayErrors($reloadCaptcha = false)
	{
		//$errors = $this->form_error;
		$js=array();
		foreach ($this->formErrors as $field => $msg){
			$js[] = $this->js(true)->atk4_form('fieldError',$field,$msg);
		}
		/*if($reloadCaptcha)
			$js[] = $this->js(null,'Recaptcha.reload()');*/
		$this->js(null,$js)->execute();
	}
}