<?php

/**
 * Adapt Agile Toolkit Jui to Wordpress
 */
class Wp_WpJui extends jUI
{
	//Only need for special testing
	//public $chain_class = 'Wp_WpJqueryChain';

	public function addDefaultIncludes(){}

	public function addInclude($file,$ext='.js')
	{
		if (strpos( $file, 'jquery' ) === false ){
			if( strpos( $file, 'http') === 0 ){
				parent::addOnReady('$.atk4.includeJS("'.$file.'")');
				return $this;
			}
			$url = $this->app->locateURL( 'js', $file.$ext );
			parent::addOnReady( '$.atk4.includeJS("'.$url.'")' );
		}
		return $this;
	}
}