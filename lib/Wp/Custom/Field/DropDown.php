<?php

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