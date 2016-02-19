<?php

/**
 * Created by abelair.
 * Date: 2016-02-15
 * Time: 1:17 PM
 */
class Model_WpUserMeta extends Model_Table
{
	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'usermeta';
		$this->id_field = 'umeta_id';
		parent::init();

		$this->addField('meta_key');
		$this->addField('meta_value');

		//Add relation with Users.
		$this->hasOne('WpUsers', 'user_id');
	}
}