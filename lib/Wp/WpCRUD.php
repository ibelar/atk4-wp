<?php

/**
 * Created by abelair.
 * Date: 2015-09-11
 * Time: 11:33 AM
 *
 * Extends regular CRUD class
 * This class use WpVirtualPage instead of the regular VirtualPage.
 */
class Wp_WpCRUD extends View_CRUD
{
	public function init()
	{
		//We still need to init the view parent.
		View::init();
		$this->js_reload = $this->js()->reload();


		// Virtual Page would receive 3 types of requests - add, delete, edit
		$this->virtual_page = $this->add('Wp_WpVirtualPage', array(
			'frame_options'=>$this->frame_options
		));

		$name_id = $this->virtual_page->name.'_id';

		//also commented on parent init ??
		/*if ($_GET['edit'] && !isset($_GET[$name_id])) {
			$_GET[$name_id] = $_GET['edit'];
		}*/


		if (isset($_GET[$name_id])) {
			$this->api->stickyGET($name_id);
			$this->id = $_GET[$name_id];
		}

		if ($this->isEditing()) {
			$this->form = $this
				->virtual_page
				->getPage()
				->add($this->form_class, $this->form_options)
				//->addClass('atk-form-stacked')
			;

			$this->grid = new Dummy();

			return;
		}

		$this->grid = $this->add($this->grid_class, $this->grid_options);
		$this->form = new Dummy();

		// Left for compatibility
		$this->js('reload', $this->grid->js()->reload());

		if ($this->allow_add) {
			$this->add_button = $this->grid->addButton('Add');
		}
	}
}