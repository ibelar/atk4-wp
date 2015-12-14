<?php

/**
 * Created by abelair.
 * Date: 2015-12-01
 * Time: 1:44 PM
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