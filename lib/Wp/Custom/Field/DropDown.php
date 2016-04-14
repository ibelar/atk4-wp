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
 * Dropdown field in Wordpress custom form.
 */
class Wp_Custom_Field_DropDown extends Wp_Custom_Field_ValueList
{
	public $selectnemu_options = array();

	function getInput($attr=array()){
		$this->js(true)->selectmenu($this->selectnemu_options);
		$multi = isset($this->attr['multiple']);
		$output=$this->getTag('select',array_merge(array(
				'name'=>$this->name . ($multi?'[]':''),
				'data-shortname'=>$this->short_name,
				'id'=>$this->name,
			),
				$attr,
				$this->attr)
		);

		foreach($this->getValueList() as $value=>$descr){
			// Check if a separator is not needed identified with _separator<
			$output.=
				$this->getOption($value)
				.$this->api->encodeHtmlChars($descr)
				.$this->getTag('/option');
		}
		$output.=$this->getTag('/select');
		return $output;
	}
	function getOption($value){
		$selected = false;
		if($this->value===null || $this->value===''){
			$selected = $value==='';
		} else {
			$selected = $value == $this->value;
		}
		return $this->getTag('option',array(
			'value'=>$value,
			'selected'=>$selected
		));
	}
}