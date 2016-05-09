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
 *  Use Custom form.
 *  Wp_Custom_Form does not contain <form> tag since Wp has already output it.
 *  Use of this form is simply a way to easily add field within a widget and continue using field functionality of an Atk Form Class.
 *
 *  Overide submitted() function in order to still use form atk4 form functionnality in WP. Because this form is not handle via ajax
 *  we need another way to deremined if it is submitted.
 */
class Form_WpDashboard extends Form_WpWidget
{
	public function init()
	{
		parent::init();
		//$this->addField('hidden', 'atk_dash_form')->set($this->name);
	}

	/**
	 * Dashboard widget form are not handle via ajax.
	 * $_GET['submit'] = $this->name is never sent.
	 * we need to check for submission using WP post submit parameter.
	 *
	 * @return bool|null|void
	 * @throws BaseException
	 * @throws Exception_ForUser
	 * @throws Exception_ValidityCheck
	 */
	public function submitted()
	{
		//Add our own submit check compatible with WP
		if ($_POST['submit'] != 'Submit') {
			return;
		}
		if (!is_null($this->bail_out)) {
			return $this->bail_out;
		}

		$this->hook('loadPOST');
		try {
			$this->hook('validate');
			$this->hook('post-validate');

			if (!empty($this->errors)) {
				return false;
			}

			if (($output = $this->hook('submit', array($this)))) {
				$has_output = false; // @todo all this [if] block logic should be re-checked, looks suspicious
				/* checking if anything usefull in output */
				if (is_array($output)) {
					$has_output = false;
					foreach ($output as $row) {
						if ($row) {
							$has_output = true;
							$output = $row;
							break;
						}
					}
					if (!$has_output) {
						return true;
					}
				}
				/* TODO: need logic re-check here + test scripts */
				//if(!is_array($output))$output=array($output);
				// already array
				if ($has_output) {
					if ($output instanceof jQuery_Chain) {
						$this->js(null, $output)->execute();
					} elseif (is_string($output)) {
						$this->js(null, $this->js()->reload())->univ()->successMessage($output)->execute();
					}
				}
			}
		} catch (BaseException $e) {
			if ($e instanceof Exception_ValidityCheck) {
				$f = $e->getField();
				if ($f && is_string($f) && $fld = $this->hasElement($f)) {
					/** @type Form_Field $fld */
					$fld->displayFieldError($e->getMessage());
				} else {
					$this->js()->univ()->alert($e->getMessage())->execute();
				}
			}
			if ($e instanceof Exception_ForUser) {
				$this->js()->univ()->alert($e->getMessage())->execute();
			}
			throw $e;
		}

		return true;
	}
}