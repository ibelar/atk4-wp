<?php

/**
 * Wordpress term table as an atk model
 */
class Model_WpTerms extends Model_Table
{
	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'terms';
		$this->id_field = 'term_id';
		parent::init();

		$this->addField('name')->caption(_('Name'));
		$this->addField('slug')->caption(_('Slug'));
		$this->addField('term_group')->caption(_('Term Group'));
	}
}