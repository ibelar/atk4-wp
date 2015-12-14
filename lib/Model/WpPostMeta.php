<?php

/**
 * Created by abelair.
 * Date: 2015-12-01
 * Time: 1:41 PM
 */
class Model_WpPostMeta extends Model_Table
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

	public function getMetaValue ( $postId, $key )
	{
		$this->addCondition( 'post_id', $postId );
		$value = $this->tryLoadBy( 'meta_key', $key )->get('meta_value');
		return maybe_unserialize( $value );
	}

	/*public function saveOptionValue ( $option, $value )
	{
		$this->tryLoadBy( 'name', $option);
		$this->set( 'value', maybe_serialize($value));
		$this->set( 'name', $option );
		$this->save();
	}*/

}