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
 * Wordpress term relationships table as an atk model
 */
class Model_WpTermRelationships extends SQL_Model
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