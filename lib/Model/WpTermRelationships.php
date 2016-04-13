<?php

/**
 * Wordpress term relationships table as an atk model
 */
class Model_WpTermRelationships extends Model_Table
{
	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'term_relationships';
		$this->id_field = 'object_id';
		parent::init();

		$this->addField('term_order');
		$this->hasOne('WpPosts', 'object_id');
		$this->hasOne('WpTermTaxonomy', 'term_taxonomy_id');
	}
}