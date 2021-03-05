<?php

// Do not allow direct access to this file.
if(!function_exists('add_action'))
	die();

class JTSidebarGeneratorCreationPage extends Page implements JTSidebarGeneratorPageInterface {

	protected $sidebarGenerator;

	public function __construct($jtSidebarGenerator) {
		$this->sidebarGenerator = $jtSidebarGenerator;
	}

	public function getTitle() {
		return __('Add new sidebar', JTSidebarGeneratorConstants::PLUGIN_NAME);
	}

	public function getActionId() {
		return 'jt_create_new_sidebar';
	}

	public function getSlug() {
		return JTSidebarGeneratorConstants::CREATION_PAGE_SLUG;
	}

	public function getParentSlug() {
		return null;
	}

	public function render() {
		$html  = '<div class="wrap"><h1 class="wp-heading-inline">' . $this->getTitle() . '</h1>';
		$html .= '<form method="post" action="admin-post.php" class="validate">';
		$html .= '<input type="hidden" name="action" value="' . $this->getActionId() . '">';
		$html .= '<table class="form-table" role="presentation"><tbody>';
		$html .= '<tr class="form-field form-required">';
		$html .= '<th scope="row"><label for="name">' . __('Name', JTSidebarGeneratorConstants::PLUGIN_NAME);
		$html .= '<span class="description"> (' . __('required', JTSidebarGeneratorConstants::PLUGIN_NAME) . ')</span></label></th>';
		$html .= '<td><input name="name" type="text" id="name" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td></tr>';
		$html .= '<tr class="form-field form-required">';
		$html .= '<th scope="row"><label for="class">' . __('Class name', JTSidebarGeneratorConstants::PLUGIN_NAME);
		$html .= '<span class="description"> (' . __('required', JTSidebarGeneratorConstants::PLUGIN_NAME) . ')</span></label></th>';
		$html .= '<td><input name="class" type="text" id="class" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60"></td></tr>';
		$html .= '</tbody></table><p class="submit">';
		$html .= '<a href="' . JTSidebarGeneratorConstants::OVERVIEW_PAGE_URL . '" class="button">' . __('Go Back', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</a> ';
		$html .= '<input type="submit" name="createsidebar" id="createsidebarsub" class="button button-primary" value="' . $this->getTitle() . '">';
		$html .= '</p></form></div>';

		echo $html;
	}

	public function handleFormRequest($request) {
		$this->sidebarGenerator->createSidebar($request['name'], $request['class']);

		wp_redirect(JTSidebarGeneratorConstants::OVERVIEW_PAGE_URL . '&success=true');
	}
}
