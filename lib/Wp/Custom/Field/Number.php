<?php

/**
 * Number field in Wordpress custom form.
 */
class Wp_Custom_Field_Number extends Wp_Custom_Field_Line
{
	public function getInput( $attr=array() )
	{
		return parent::getInput( array_merge( array('type'=>'number'), $attr ));
	}
}