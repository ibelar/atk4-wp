<?php

/**
 * Created by abelair.
 * Date: 2016-03-28
 * Time: 8:16 AM
 */
class Wp_Widget_Field_Date extends Wp_Widget_Field
{
	public function getInput( $attr=array() )
	{
		return parent::getInput( array_merge( array('type'=>'date'), $attr ));
	}
}