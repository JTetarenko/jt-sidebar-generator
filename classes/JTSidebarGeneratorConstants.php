<?php

if(!function_exists('add_action'))
	die();

class JTSidebarGeneratorConstants {
	const PLUGIN_NAME = 'jt-sidebar-generator';
	const OPTIONS_STORE_ID = 'jt_sidebar_generator_data';
	const OVERVIEW_PAGE_SLUG = 'jt-sidebar-generator-overview';
	const CREATION_PAGE_SLUG = 'jt-sidebar-generator-add-new';
	const OVERVIEW_PAGE_URL = "themes.php?page=" . self::OVERVIEW_PAGE_SLUG;
	const CREATION_PAGE_URL = "themes.php?page=" . self::CREATION_PAGE_SLUG;
	const WIDGET_REPLACEABLE_STORE_ID = 'jt_sidebar_generator_widget_replaceable_data';
}
