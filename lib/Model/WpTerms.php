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
 * Wordpress term table as an atk model
 */
class Model_WpTerms extends SQL_Model
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