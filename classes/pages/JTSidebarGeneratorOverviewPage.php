<?php

// Do not allow direct access to this file.
if(!function_exists('add_action'))
	die();

class JTSidebarGeneratorOverviewPage extends Page implements JTSidebarGeneratorPageInterface {

	protected $sidebarGenerator;

	public function __construct($jtSidebarGenerator) {
		$this->sidebarGenerator = $jtSidebarGenerator;
	}

	public function getTitle() {
		return __('Sidebar Generator', JTSidebarGeneratorConstants::PLUGIN_NAME);
	}

	public function getActionId() {
		return 'jt_remove_sidebar';
	}

	public function getSlug() {
		return JTSidebarGeneratorConstants::OVERVIEW_PAGE_SLUG;
	}

	public function getParentSlug() {
		return 'themes.php';
	}

	public function render() {
		$html  = '<div class="wrap"><h1 class="wp-heading-inline">' . $this->getTitle() . '</h1>';
		$html .= '<span class="split-page-title-action"><a class="button" href="' . JTSidebarGeneratorConstants::CREATION_PAGE_URL . '">'
		         . __('Add new', JTSidebarGeneratorConstants::PLUGIN_NAME) .'</a></span>';

		if (isset($_GET['success'])) {
			$html .= '<div class="updated"><p>'
			         . __('New Sidebar successfully created!', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</p></div>';
		}

		if (isset($_GET['deleted'])) {
			$html .= '<div class="updated"><p>'
			         . __('The Sidebar successfully deleted!', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</p></div>';
		}

		$html .= '<hr class="wp-header-end"><table class="wp-list-table widefat fixed striped table-view-list">';
		$html .= '<thead><tr>';
		$html .= '<td>' . __('Name', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</td>';
		$html .= '<td>' . __('CSS class', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</td>';
		$html .= '<td>' . __('Actions', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</td>';
		$html .= '</tr></thead>';

		$sidebars = $this->sidebarGenerator->getSidebars();

		if ($sidebars) {
			foreach ($sidebars as $sidebar) {
				$html .= '<tr>';
				$html .= '<td>' . $sidebar->name . '</td>';
				$html .= '<td>' . $sidebar->class . '</td>';
				$html .= '<td><form method="post" action="admin-post.php">';
				$html .= '<input type="hidden" name="action" value="' . $this->getActionId() . '">';
				$html .= '<input type="hidden" name="id" value="' . $sidebar->id . '">';
				$html .= '<input type="submit" class="button-link button-link-delete" value="' . __( 'Remove', JTSidebarGeneratorConstants::PLUGIN_NAME ) . '"></form></td>';
				$html .= '</tr>';
			}
		}

		$html .= '</tbody></table></div>';

		echo $html;
	}

	public function handleFormRequest($request) {
		$this->sidebarGenerator->removeSidebar($request['id']);

		wp_redirect(JTSidebarGeneratorConstants::OVERVIEW_PAGE_URL . '&deleted=true');
	}
}
