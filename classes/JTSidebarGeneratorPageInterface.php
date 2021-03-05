<?php

// Do not allow direct access to this file.
if(!function_exists('add_action'))
	die();

interface JTSidebarGeneratorPageInterface {

	public function __construct($jtSidebarGenerator);

	public function getTitle();

	public function getActionId();

	public function getParentSlug();

	public function getSlug();

	public function render();

	public function handleFormRequest($request);
}
