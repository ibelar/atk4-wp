<?php

/**
 * Date field in Wordpress custom form.
 */
class Wp_Custom_Field_Date extends Wp_Custom_Field
{
	public function getInput( $attr=array() )
	{
		return parent::getInput( array_merge( array('type'=>'date'), $attr ));
	}
}