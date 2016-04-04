<?php

/**
 * Created by abelair.
 * Date: 2016-03-25
 * Time: 1:35 PM
 */
class Wp_Widget_Field_Number extends Wp_Widget_Field_Line
{
	public function getInput( $attr=array() )
	{
		return parent::getInput( array_merge( array('type'=>'number'), $attr ));
	}
}