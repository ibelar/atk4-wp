<?php

/**
 * Wordpress term taxonomy table as an atk model
 */
class Model_WpTermTaxonomy extends Model_Table
{
	public function init()
	{

		$this->table = WpHelper::getDbPrefix() . 'term_taxonomy';
		$this->id_field = 'term_taxonomy_id';
		parent::init();

		$this->addField('taxonomy')->caption(_('Taxonomy'));
		$this->title_field = 'taxonomy';

		$this->addField('description')->caption(_('Description'));
		$this->addField('parent')->caption(_('Parent'));
		$this->addField('count')->caption(_('Count'));

		$this>hasOne('WpTerms', 'term_id');

	}
}