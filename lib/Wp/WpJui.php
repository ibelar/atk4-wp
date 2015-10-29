<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-08-26
 * Time: 10:19 AM
 */
class Wp_WpJui extends jUI
{
	public $chain_class = 'Wp_WpJqueryChain';

	function addDefaultIncludes(){}

	function addInclude($file,$ext='.js')
	{
		if (strpos( $file, 'jquery' ) === false ){
			if(strpos($file,'http')===0){
				parent::addOnReady('$.atk4.includeJS("'.$file.'")');
				return $this;
			}
			$url=$this->api->locateURL('js',$file.$ext);


			parent::addOnReady('$.atk4.includeJS("'.$url.'")');
		}

		return $this;
	}

}