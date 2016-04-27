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
 * Adapt Agile Toolkit Jui to Wordpress
 */
class Wp_WpJui extends jUI
{
	//Only need for special testing
	//public $chain_class = 'Wp_WpJqueryChain';

	public function addDefaultIncludes(){}

	public function addInclude($file, $ext='.js')
	{
		if (strpos($file, 'jquery') === false) {
			if (strpos($file, 'http') === 0) {
				parent::addOnReady('$.atk4.includeJS("'.$file.'")');
				return $this;
			}
			$url = $this->app->locateURL( 'js', $file.$ext );
			parent::addOnReady( '$.atk4.includeJS("'.$url.'")' );
		}
		return $this;
	}
}