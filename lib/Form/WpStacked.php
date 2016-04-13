<?php

/**
 * Simply using the stacked template with Form_WpForm
 */
class Form_WpStacked extends Form_WpForm
{
	public function defaultTemplate()
	{
		return array('form/stacked');
	}
}