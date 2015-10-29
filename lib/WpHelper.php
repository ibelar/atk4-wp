<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-09-10
 * Time: 10:52 AM
 */
class WpHelper
{

	public static function getDbPrefix()
	{
		global $wpdb;
		return $wpdb->prefix;
	}


}