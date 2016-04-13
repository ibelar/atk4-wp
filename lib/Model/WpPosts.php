<?php

/**
 * Wordpress Post table as an atk model.
 *
 */

class Model_WpPosts extends Model_Table
{
	public function init()
	{
		$this->table = WpHelper::getDbPrefix() . 'posts';
		$this->id_field = 'ID';
		parent::init();

		$this->addField('post_name');
		$this->title_field ='post_name';

		$this->addField('post_author');
		$this->addField('post_date');
		$this->addField('post_date_gmt');
		$this->addField('post_content');
		$this->addField('post_title');
		$this->addField('post_excerpt');
		$this->addField('post_status');
		$this->addField('comment_status');
		$this->addField('ping_status');
		$this->addField('post_password');
		$this->addField('to_ping');
		$this->addField('pinged');
		$this->addField('post_modified');
		$this->addField('post_modified_gmt');
		$this->addField('post_content_filtered');
		$this->addField('post_parent');
		$this->addField('guid');
		$this->addField('menu_order');
		$this->addField('post_type');
		$this->addField('post_mime_type');
		$this->addField('comment_count');
	}
}