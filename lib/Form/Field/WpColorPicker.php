<?php

/**
 * Created by abelair.
 * Date: 2016-05-04
 * Time: 11:08 AM
 */
class Form_Field_WpColorPicker extends Form_Field_Line
{
	public $options = null;

	public function getInput($attr = array())
	{
		global $wp_scripts, $wp_styles;

		if (!in_array('wp-color-picker', $wp_scripts->queue) && !$this->app->ajaxMode)
			throw $this->exception(_('You need to include "wp-color-picker.js" file prior to use this field'))->addMoreInfo(_('Note'), _('You can add it via js-inc from your configuration file.'));
		if (!in_array('wp-color-picker', $wp_styles->queue) && !$this->app->ajaxMode)
			throw $this->exception(_('You need to include "wp-color-picker.css" file prior to use this field'))->addMoreInfo(_('Note'), _('You can add it via css-inc from your configuration file.'));

		$this->addClass('atk4wp-color-picker');
		$this->js(true)->_css('wp-colorpicker');

		$this->js(true)->wpColorPicker($this->options);
		return parent::getInput($attr);
	}

	public function performValidation()
	{
		// check if user insert a HEX color with #
		if (!preg_match('/^#[a-f0-9]{6}$/i', $this->get())) {
			$errorMsg = _('Please indicate an hexadecimal color value using #. (Ex: #dd33ee)');

			if ($this->form instanceof Form_WpForm) {
				$this->form->formErrors[$this->short_name] = $errorMsg;
			} else {
				$this->form->error($this->short_name, $errorMsg);
			}
		}

	}
}