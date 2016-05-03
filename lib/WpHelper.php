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
 * Some helper function.
 */
class WpHelper
{
	static $jQueryVar = 'jQuery';
	static $jQueryBundle = 'wp-atk4-bundle-jquery.min';

	public static function getDbPrefix()
	{
		global $wpdb;
		return $wpdb->prefix;
	}

	public static function getPageId()
	{
		global $post;
		if (is_home()) {
			return 'home';
		} else {
			return $post->ID;
		}
	}

	public static function getWpOption($option)
	{
		return get_option($option);
	}

	public static function getJQueryVar()
	{
		return self::$jQueryVar;
	}

	public static function getJQueryBundle()
	{
		return self::$jQueryBundle;
	}


}