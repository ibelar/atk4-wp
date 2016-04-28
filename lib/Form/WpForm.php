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
 * atk form extension.
 *
 * Field error are accumulated in $formErrors array
 * When form is submitted, a call to $form->exitOnError() will display all errors to user
 * and aborted form submission.
 *
 * 2016-04-27: Move $form->exitOnError() to post-validate hook. No need to explicitly call it.
 */
class Form_WpForm extends Form
{

	public $formErrors = array();

	public function init()
	{
		parent::init();
		$this->addHook('post-validate', function() {
			$this->exitOnError();
		});
	}

	public function addMandatoryField($type, $option=null, $caption=null, $attr=null)
	{
		return $this->makeFieldMandatory($this->addField($type, $option, $caption, $attr));
	}

	protected function makeFieldMandatory($field)
	{
		$field->addHook('validate', [$this, 'validateMandatory']);
		return $field;
	}

	public function validateMandatory($field)
	{
		$value = trim($field->get());
		if ($value==="" || is_null($value)) {
			$this->formErrors[$field->short_name] = _('This is a mandatory field');
		}
	}

	/**
	 * Allow to set an error from field by calling
	 *  $field->form->setFormError()
	 * @param $field
	 * @param $error
	 */
	public function setFormError( $fieldName, $error )
	{
		$this->formErrors[$fieldName] = $error;
	}

	public function exitOnError()
	{
		if ($this->hasError()) {
			$this->displayErrors();
		}
	}

	public function hasError()
	{
		return isset($this->formErrors) && !empty($this->formErrors);
	}

	public function displayFormErrors()
	{
		$this->displayErrors();
	}

	/**
	 * Manually call hook validate on each field for backward compatibility in 4.3.2
	 * @throws BaseException
	 */
	/*public function performValidation()
	{
		foreach ($this->elements as $x=>$field) {
			if ($field instanceof \Form_Field) {
				$field->hook('validate');
			}
		}
	}*/

	private function displayErrors()
	{
		$js=array();
		foreach ($this->formErrors as $field => $msg) {
			$js[] = $this->js(true)->atk4_form('fieldError', $field, $msg);
		}
		$this->js(null,$js)->execute();
	}
}