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
 * Wordpress Post Meta table as an atk model
 */
class Model_WpPostMeta extends SQL_Model
{
	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'postmeta';
		$this->id_field = 'meta_id';
		parent::init();

		$this->addField('meta_key');
		$this->addField('meta_value');


		$this->hasOne('WpPosts', 'post_id');
	}

	public function getMetaValue($postId, $key)
	{
		$this->addCondition('post_id', $postId);
		$value = $this->tryLoadBy('meta_key', $key)->get('meta_value');
		return maybe_unserialize($value);
	}

	/*public function saveOptionValue ( $option, $value )
	{
		$this->tryLoadBy( 'name', $option);
		$this->set( 'value', maybe_serialize($value));
		$this->set( 'name', $option );
		$this->save();
	}*/

}