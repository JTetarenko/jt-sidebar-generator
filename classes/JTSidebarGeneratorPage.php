<?php

class Page {

	public function init() {
		add_action('admin_menu', function() {
			add_submenu_page(
				$this->getParentSlug(),
				$this->getTitle(),
				$this->getTitle(),
				'manage_options',
				$this->getSlug(),
				[$this, 'render']
			);
		});

		add_action('admin_post_' . $this->getActionId(), function() {
			$this->handleFormRequest($_POST);
		});
	}
}
