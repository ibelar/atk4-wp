<?php

/**
 * Created by abelair.
 * Date: 2015-12-01
 * Time: 1:28 PM
 *
 `ID` = <{ID: }>,
`post_author` = <{post_author: 0}>,
`post_date` = <{post_date: 0000-00-00 00:00:00}>,
`post_date_gmt` = <{post_date_gmt: 0000-00-00 00:00:00}>,
`post_content` = <{post_content: }>,
`post_title` = <{post_title: }>,
`post_excerpt` = <{post_excerpt: }>,
`post_status` = <{post_status: publish}>,
`comment_status` = <{comment_status: open}>,
`ping_status` = <{ping_status: open}>,
`post_password` = <{post_password: }>,
`post_name` = <{post_name: }>,
`to_ping` = <{to_ping: }>,
`pinged` = <{pinged: }>,
`post_modified` = <{post_modified: 0000-00-00 00:00:00}>,
`post_modified_gmt` = <{post_modified_gmt: 0000-00-00 00:00:00}>,
`post_content_filtered` = <{post_content_filtered: }>,
`post_parent` = <{post_parent: 0}>,
`guid` = <{guid: }>,
`menu_order` = <{menu_order: 0}>,
`post_type` = <{post_type: post}>,
`post_mime_type` = <{post_mime_type: }>,
`comment_count` = <{comment_count: 0}>
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