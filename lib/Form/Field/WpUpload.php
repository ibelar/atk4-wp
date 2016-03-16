<?php

/**
 * Created by abelair.
 * Date: 2016-02-19
 * Time: 9:49 AM
 *
 *
 *
 */
class Form_Field_WpUpload extends Form_Field
{

	public $options;
	public $max_file_size;
	public $inputMessage;

	public function init()
	{
		parent::init();
		$this->owner->template->set('enctype', "enctype=\"multipart/form-data\"");
		$this->attr['type']   ='file';
		$this->attr['class']  = 'inputfile';
		$this->inputMessage = _('Upload file');

		$max_post   = $this->convertToBytes(ini_get('post_max_size'))/2;
		$max_upload = $this->convertToBytes(ini_get('upload_max_filesize'));

		$this->max_file_size = min($max_upload, $max_post);
	}

	public function getInput()
	{
		$this->options['form'] = $this->owner;
		$this->options['maxSize'] = $this->max_file_size;

		$this->js(true)->_load('ui.wp-uploader')->wp_uploader($this->options);
		$this->js(true)->_css('wp-uploader');

		$o  = parent::getInput();
		$o .= "<label for='{$this->name}'><span class=\"icon-upload\"></span> </label>";
		$o .= "<div class='input-message'><span >{$this->inputMessage}</span></div>";
		$o .= '<div class="client-error-template"><p class="atk-form-error"><span class="field-error-text"></span></p></div>';
		return $o;

	}

	public function addProgressBar( $color = null, $borderSize= null, $borderColor=null, $borderType=null)
	{
		$holder = $this->add('View', 'pg', 'below_field')->addClass('progress-bar');
		$holder->add('View', 'pg-d')->addClass('bar');
		$this->options['progress'] = $holder->name;
		if( isset ($color )){
			$this->options['barColor'] = $color;
		}
		if( isset ($borderSize )){
			$this->options['borderSize'] = $borderSize;
		}
		if( isset ($borderColor )){
			$this->options['borderColor'] = $borderColor;
		}
		if( isset ($borderType )){
			$this->options['borderType'] = $borderType;
		}
		return $this;
	}

	public function setAcceptType ( Array $type )
	{
		$this->attr['accept'] = $this->stringifyAccept ( $type );
		return $this;
	}

	/**
	 * @param $class
	 */
	public function addStyleClass( $class )
	{
		$this->attr['class'] .= ' '.$class;
		return $this;
	}

	public function setInputMessage( $msg = null )
	{
		if( isset ($msg)){
			$this->inputMessage = $msg;
		}
		return $this;
	}
	public function setMultiple()
	{
		$this->attr['multiple'] = null;
		return $this;
	}

	private function stringifyAccept( $accepts )
	{
		return implode(',', $accepts);
	}

	private function convertToBytes($val)
	{
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch($last) {
            case 'g':
                $size = $val * 1024 * 1024 * 1024;
                break;
            case 'm':
                $size = $val * 1024 * 1024;
                break;
            case 'k':
                $size = $val * 1024;
                break;
            default:
                $size = (int) $val;
        }
	return $size;
	}
}