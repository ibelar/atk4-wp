<?php

/**
 * Created by abelair.
 * Date: 2015-09-11
 * Time: 11:37 AM
 *
 * Extends regular VirtualPage.
 */
class Wp_WpVirtualPage extends VirtualPage
{
	public function init()
	{
		parent::init();
		$this->page_class = 'Wp_WpPanel';
	}
	/**
	 * Redefine getPage function.
	 * There is no atk4 page in Wordpress. Need to use panel instead.
	 * @return mixed
	 */
	public function getPage()
	{
		$this->api->page_object->destroy(false);

		$this->api->page_object = $this->page = $this->api->add(
			$this->page_class,
			$this->name/*,
			null,
			$this->page_template*/
		);

		$this->api->stickyGET($this->name);
		return $this->page;
	}

	/**
	 * Associates code with the page. This code will be executed within
	 * a brand new page when called by URL.
	 *
	 * @param callable $method_or_arg Optional argument
	 * @param callable $method        function($page){ .. }
	 *
	 * @return VirtualPage $this
	 */
	function set($method_or_arg, $method = null)
	{

		$method = is_callable($method_or_arg)?$method_or_arg:$method;
		$arg    = is_callable($method_or_arg)?null:$method_or_arg;

		$self=$this;

		if ($this->isActive($arg)) {
			$this->api->addHook('post-init', function () use ($method, $self) {
				$page = $self->getPage();
				$page->id=$_GET[$self->name.'_id'];
				$self->api->stickyGET($self->name.'_id');

				try {
					call_user_func($method, $page, $self);
				} catch (Exception $e){
					// exception occured possibly due to a nested page. We
					// are already executing from post-init, so
					// it's fine to ignore it.
				}

				//Imants: most likely forgetting is not needed, because we stop execution anyway
				//$self->api->stickyForget($self->name.'_id');
				//$self->api->stickyForget($self->name);
			});
			throw $this->exception('', 'StopInit');
		}
		return $this;
	}
}