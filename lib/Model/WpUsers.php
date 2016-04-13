<?php

/**
 * Wordpress users table as an atk model
 */
class Model_WpUsers extends Model_Table
{
	public function init()
	{

		$this->table = WpHelper::getDbPrefix() . 'users';
		$this->id_field = 'ID';
		$this->title_field ='display_name';
		parent::init();

		$this->addField('user_login');
		$this->addField('user_pass');
		$this->addField('user_nicename');
		$this->addField('user_email');
		$this->addField('user_url');
		$this->addField('user_registered');
		$this->addField('user_activation_key');
		$this->addField('user_status');
		$this->addField('display_name');


	}
}