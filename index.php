<?php
/*
 * Plugin Name: Sidebar Generator
 * Description: Creates widgets to use for pages
 */

// Do not allow direct access to this file.
if(!function_exists('add_action'))
	die();

$pluginPath = plugin_dir_path( __FILE__ );

include_once $pluginPath . '/classes/helpers/JTSidebarGeneratorDebugHelper.php';
include_once $pluginPath . '/classes/helpers/JTSidebarGeneratorPageSettingsHelper.php';

include_once $pluginPath . '/classes/JTSidebarGeneratorConstants.php';
include_once $pluginPath . '/classes/JTSidebarGenerator.php';
include_once $pluginPath . '/classes/JTSidebarGeneratorPageInterface.php';
include_once $pluginPath . '/classes/JTSidebarGeneratorPage.php';

include_once $pluginPath . '/classes/pages/JTSidebarGeneratorOverviewPage.php';
include_once $pluginPath . '/classes/pages/JTSidebarGeneratorCreationPage.php';

$sidebarGenerator = new JTSidebarGenerator();

$sidebarGenerator->init();

$pages = [
	new JTSidebarGeneratorOverviewPage($sidebarGenerator),
	new JTSidebarGeneratorCreationPage($sidebarGenerator)
];

foreach($pages as $page) {
	$page->init();
}
