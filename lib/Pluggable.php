<?php

/**
 * Created by PhpStorm.
 * User: abelair
 * Date: 2015-09-08
 * Time: 3:35 PM
 */
interface Pluggable
{
	public function __construct( $name, $path);
	public function activatePlugin();
	public function deactivatePlugin();
	public function uninstallPlugin();

}