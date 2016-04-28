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
 * Wordpress term taxonomy table as an atk model
 */
class Model_WpTermTaxonomy extends SQL_Model
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