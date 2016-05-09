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
 * Wordpress Options table as an atk model.
 */
class Model_WpOptions extends SQL_Model
{

	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'options';
		$this->id_field = 'option_id';
		parent::init();

		$this->addField('name', 'option_name');
		$this->addField('value', 'option_value');
		$this->addField('autoload');
	}

	public function getOptionValue($option, $default = null)
	{
		$value = $this->tryLoadBy('name', $option)->get('value');
		if (isset($value)) {
			$value = maybe_unserialize($value);
		} else {
			$value = $default;
		}
		return $value;
	}

	public function saveOptionValue($option, $value)
	{
		$this->tryLoadBy('name', $option);
		$this->set('value', maybe_serialize($value));
		$this->set('name', $option);
		$this->save();
	}
}