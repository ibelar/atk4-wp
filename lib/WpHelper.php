<?php

/**
 * Some helper function.
 */
class WpHelper
{

	public static function getDbPrefix()
	{
		global $wpdb;
		return $wpdb->prefix;
	}

	public static function getPageId()
	{
		global $post;
		if( is_home() ){
			return 'home';
		} else {
			return $post->ID;
		}
	}

	public static function getWpOption ( $option )
	{
		return get_option( $option );
	}


}