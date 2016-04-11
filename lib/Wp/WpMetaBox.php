<?php

/**
 * Metabox are Panel output to Wordpress.
 *
 * MetaBox are already include in a Wordpress form in order to collect
 * post meta. This means that adding a form to an atk4 view set as metabox required
 * a custom form (Form_WpMetaBox). This form has no form tag and field added to this form
 * are adapted to Wordpress field.
 *
 * Adding a form also required a pre-render hook for setting the form field value for allowing
 * user to add field after the form is added.
 *
 */
class Wp_WpMetaBox extends Wp_WpPanel
{
	//Post that is associate with this Wp metabox.
	//Type Wp_Post
	public $post = null;

	//Argument passed to the metaBox via configuration
	public $args = null;

	public $form = null;


	/**
	 * metaBox initialisation
	 */
	public function init()
	{
		parent::init();
		$this->post = $this->owner->metaBox['post'];
		$this->args = $this->owner->metaBox['args'];
	}

	/**
	 * Added a form to this metaBox for adding meta data to Wp post.
	 *
	 * @param null $form
	 * @param null $option
	 * @param null $spot
	 * @param null $template
	 *
	 * @return AbstractObject|null
	 * @throws BaseException
	 */
	public function addForm( $form = null, $option = null, $spot = null, $template = null )
	{
		if( !isset ($form)){
			$form = 'Form_WpMetaBox';
		}
		$this->form = $this->add( $form, $option, $spot, $template);
		//Since we do not know which field will be added, delay the setting of field
		$this->app->addHook('pre-render', [ $this, 'setFormField']);
		return $this->form;
	}

	/**
	 * Called from the action hook added by the MetaBox controller.
	 * @param $postId
	 */
	public function savePost( $postId )
	{
		if( isset ($this->form )){
			foreach($this->form->elements as $x => $field){
				if($field instanceof \Form_Field){
					update_post_meta( $postId, $field->name, strip_tags( $_POST[ $field->name ]));
				}
			}
		}
	}

	/**
	 * Will read post meta data and set field value accordingly.
	 */
	public function setFormField()
	{
		if( isset ($this->form ) && isset( $this->post ) ){
			foreach($this->form->elements as $x => $field){
				if($field instanceof \Form_Field){
					$field->set( get_post_meta( $this->post->ID, $field->name, true ));
				}
			}
		}
	}
}