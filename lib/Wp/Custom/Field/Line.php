<?php

/**
 * Line field in Wordpress custom form.
 */
class Wp_Custom_Field_Line extends Wp_Custom_Field
{
	public function getInput( $attr=array() )
	{
		return parent::getInput( array_merge( array('type'=>'text'), $attr ));
	}
}