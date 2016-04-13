<?php

/**
 * Interface for creating Plugin using atk.
 */
interface Pluggable
{
	public function __construct( $name, $path);
	public function activatePlugin();
	public function deactivatePlugin();
	public function uninstallPlugin();

}