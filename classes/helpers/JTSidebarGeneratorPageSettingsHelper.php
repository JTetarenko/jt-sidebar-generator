<?php

class JTSidebarGeneratorPageSettingsHelper {

	public static function renderSidebarSelect($label, $inputName, $postId, $selectedOptionCallback, $sidebars, $forReplaceable = false) {
		$html  = '<p><label><strong>' . $label . '</strong></label></p>';
		$html .= '<select name="' . $inputName . '" id="jt-sidebar-select">';
		$html .= '<option value="none" ' . $selectedOptionCallback('none', $postId, $forReplaceable) . '>'
		         . __('None', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</option>';

		foreach($sidebars as $sidebar) {
			if (!$forReplaceable)
				$html .= '<option value="' . $sidebar->id . '" ' . $selectedOptionCallback($sidebar->id, $postId)
				         . '>' . $sidebar->name . '</option>';
			else
				$html .= '<option value="' . $sidebar['id'] . '" ' . $selectedOptionCallback($sidebar['id'], $postId, true)
				         . '>' . $sidebar['name'] . '</option>';
		}

		$html .= '</select>';

		echo $html;
	}
}
