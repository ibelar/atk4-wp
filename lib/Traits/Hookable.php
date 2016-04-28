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
 * Allow to add and call hooks.
 * Very similar to atk4 hook
 * Can be use in Wordpress Widget class or our own defined class.
 *
 * Use in: WpWidget.
 *
 */
trait Traits_Hookable
{
	public $hooks = [];

	/**
	 * Call a hook spot.
	 * @param $spot
	 * @param null $args
	 *
	 * @return mixed|null
	 */
	public function hook($spot, $args=null)
	{
		$result = null;
		if (isset($this->hooks[$spot])) {
			$hook = $this->hooks[$spot];
			$result = call_user_func_array(	$hook, $args);
		}
		return $result;
	}

	/**
	 * Ad a hook spot.
	 * @param $hook
	 * @param $callback
	 */
	public function addHook($hook, $callback)
	{
		if (method_exists($callback[0], $callback[1]) && is_callable([$callback[0], $callback[1]])){
			$this->hooks[$hook] = $callback;
		}
	}
}