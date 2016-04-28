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
 * Wordpress user meta table as an atk model
 */
class Model_WpUserMeta extends SQL_Model
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