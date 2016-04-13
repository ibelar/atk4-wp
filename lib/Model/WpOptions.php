<?php

/**
 * Wordpress Options table as an atk model.
 */
class Model_WpOptions extends Model_Table
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

	public function getOptionValue ( $option )
	{
		/*$this->addCondition( 'name', $option );
		$value = $this->loadAny()->get( 'value');*/
		$value = $this->tryLoadBy( 'name', $option)->get('value');
		return maybe_unserialize( $value );
	}

	public function saveOptionValue ( $option, $value )
	{
		$this->tryLoadBy( 'name', $option);
		$this->set( 'value', maybe_serialize($value));
		$this->set( 'name', $option );
		$this->save();
	}
}