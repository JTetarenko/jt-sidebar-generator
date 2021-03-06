<?php

// Do not allow direct access to this file.
if(!function_exists('add_action'))
	die();

class JTSidebarGenerator {

	public function debug($data = array(), $title = ''){
		if( is_array($data) ){
			array_walk_recursive( $data, array( $this, 'debugFilter' ) );
		}
		if( !empty($title) ){
			echo '<h3>'. $title .'</h3>';
		}
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	public function debugFilter(&$data){
		$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	}

	public function getSidebars() {
		return json_decode(get_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID));
	}

	public function getReplaceableWidgets() {
		return json_decode(get_option(JTSidebarGeneratorConstants::WIDGET_REPLACEABLE_STORE_ID));
	}

	public function createSidebar($name, $className) {
		$sidebars = $this->getSidebars();

		// If it's empty
		if (!$sidebars) {
			$result = [
				[
					'id'    => uniqid(),
					'name'  => $name,
					'class' => $className,
					'pages' => []
				]
			];

			if (empty($sidebars))
				update_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID, json_encode($result));
			else
				add_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID, json_encode($result));

			return;
		}

		$sidebars[] = [
			'id' => uniqid(),
			'name'  => $name,
			'class' => $className,
			'pages' => []
		];

		update_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID, json_encode($sidebars));
	}

	public function removeSidebar($id) {
		$sidebars = $this->getSidebars();

		if (!$sidebars) {
			return;
		}

		$result = [];
		foreach($sidebars as $sidebar) {
			if ($sidebar->id !== $id)
				$result[] = $sidebar;
		}

		update_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID, json_encode($result));
	}

	public function getSidebarByPostId($postId) {
		$sidebars = $this->getSidebars();

		if (!$sidebars && empty($sidebars)) {
			return null;
		}

		$id = null;

		foreach($sidebars as $sidebar) {
			foreach($sidebar->pages as $page) {
				if ((string)$page === (string)$postId) {
					$id = $sidebar->id;
					break;
				}
			}

			if (!is_null($id)) {
				break;
			}
		}

		return $id;
	}

	public function getReplaceableByPostId($postId) {
		$widgets = $this->getReplaceableWidgets();

		if (!$widgets && empty($widgets)) {
			return null;
		}

		$id = null;

		foreach($widgets as $widget) {
			foreach($widget->pages as $page) {
				if ((string)$page === (string)$postId) {
					$id = $widget->id;
					break;
				}
			}

			if (!is_null($id)) {
				break;
			}
		}

		return $id;
	}

	private function removePageFromSidebar($sidebar, $postId) {
		$pages = [];
		foreach($sidebar->pages as $page) {
			if ((string)$page !== (string)$postId) {
				$pages[] = $page;
			}
		}

		$sidebar->pages = $pages;

		return $sidebar;
	}

	public function addConditionToSidebar($sidebarId, $widgetId, $postId) {
		$sidebars = $this->getSidebars();

		if (!$sidebars && empty($sidebars)) {
			return;
		}

		foreach($sidebars as &$sidebar) {
			$sidebar = $this->removePageFromSidebar($sidebar, $postId);

			if ( $sidebar->id === $sidebarId ) {
				// Append postId for sidebar
				$sidebar->pages[] = $postId;
			}
		}

		update_option(JTSidebarGeneratorConstants::OPTIONS_STORE_ID, json_encode($sidebars));

		$widgets = $this->getReplaceableWidgets();

		if (!$widgets && $widgetId !== 'none') {
			$widgets = [
				[
					'id' => $widgetId,
					'pages' => [$postId]
				]
			];

			if (empty($widgets))
				update_option(JTSidebarGeneratorConstants::WIDGET_REPLACEABLE_STORE_ID, json_encode($widgets));
			else
				add_option(JTSidebarGeneratorConstants::WIDGET_REPLACEABLE_STORE_ID, json_encode($widgets));

			return;
		}

		foreach($widgets as &$widget) {
			$widget = $this->removePageFromSidebar($widget, $postId);

			if ((string)$widget->id === (string)$widgetId) {
				$widget->pages[] = $postId;
				break;
			}
		}

		if ($widgetId === 'none')
			return;

		update_option(JTSidebarGeneratorConstants::WIDGET_REPLACEABLE_STORE_ID, json_encode($widgets));
	}

	public function getSelectedOption($sidebarId, $postId) {
		$selectedValue = $this->getSidebarByPostId($postId);

		if ($sidebarId === 'none') {
			return is_null($selectedValue) ? 'selected' : '';
		}

		return $selectedValue === $sidebarId ? 'selected' : '';
	}

	public function getSelectedOptionForReplaceable($widgetId, $postId) {
		$selectedValue = $this->getReplaceableByPostId($postId);

		if ($widgetId === 'none') {
			return is_null($selectedValue) ? 'selected' : '';
		}

		return $selectedValue === $widgetId ? 'selected' : '';
	}

	public function getSelectInputName() {
		return 'jt_sidebar_selected';
	}

	public function getReplaceableInputName() {
		return 'jt_sidebar_replaceable_widget_selected';
	}

	private function getSidebarsWidgetsWithoutGeneratedOnes($sidebars) {
		global $wp_registered_sidebars;
		$allSidebars = [];

		if ($wp_registered_sidebars && !is_wp_error($wp_registered_sidebars)) {
			foreach ($wp_registered_sidebars as $sidebar) {
				$allSidebars[$sidebar['id']] = $sidebar;
			}

		}

		$static = [];
		foreach ($allSidebars as $id => $value) {
			$erase = false;
			foreach ($sidebars as $generatedSidebar) {
				if ($id === $generatedSidebar->id) {
					$erase = true;
					break;
				}
			}
			if (!$erase) {
				$static[$id] = $value;
			}
		}

		return $static;
	}

	public function appendSidebarSelectToPageSettingsMeta($post) {
		$sidebars = $this->getSidebars();

		// Make sure if we have valid sidebars and in page
		if ((!$sidebars && empty($sidebars)) || $post->post_type !== 'page')
			return;

		$html  = '<p><label><strong>Sidebar</strong></label></p>';
		$html .= '<select name="' . $this->getSelectInputName() . '" id="jt-sidebar-select">';
		$html .= '<option value="none" ' . $this->getSelectedOption('none', $post->ID) . '>'
		         . __('None', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</option>';

		foreach($sidebars as $sidebar) {
			$html .= '<option value="' . $sidebar->id . '" ' . $this->getSelectedOption($sidebar->id, $post->ID)
			         . '>' . $sidebar->name . '</option>';
		}

		$html .= '</select>';

		$html .= '<p><label><strong>Replacable Sidebar Widget</strong></label></p>';
		$html .= '<select name="' . $this->getReplaceableInputName() . '" id="jt-sidebar-replaceable-select">';
		$html .= '<option value="none" ' . $this->getSelectedOptionForReplaceable('none', $post->ID) . '>'
		         . __('None', JTSidebarGeneratorConstants::PLUGIN_NAME) . '</option>';

		$widgets = $this->getSidebarsWidgetsWithoutGeneratedOnes($sidebars);

		foreach($widgets as $widget) {
			$html .= '<option value="' . $widget['id'] . '" ' . $this->getSelectedOptionForReplaceable($widget['id'], $post->ID)
			         . '>' . $widget['name'] . '</option>';
		}

		$html .= '</select>';

		echo $html;
	}

	public function handlePageSavingRequest($postId, $request) {
		// Make sure data have values
		// Info: https://developer.wordpress.org/reference/hooks/save_post_post-post_type/#comment-2815
		if (count($request) < 1)
			return;

		$this->addConditionToSidebar(
			$request[$this->getSelectInputName()],
			$request[$this->getReplaceableInputName()],
			$postId
		);
	}

	public function registerGeneratedSidebars() {
		$sidebars = $this->getSidebars();

		// Make sure if we have valid sidebars
		if ($sidebars && !empty($sidebars)){
			// Register each sidebar
			foreach ($sidebars as $sidebar) {
				register_sidebar(
					array(
						'name'          => $sidebar->name,
						'id'            => $sidebar->id,
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>'
					)
				);
			}
		}
	}

	public function init() {
		add_action('widgets_init', [$this, 'registerGeneratedSidebars']);

		add_action('siteorigin_settings_after_page_settings_meta_box', [
			$this, 'appendSidebarSelectToPageSettingsMeta'
		]);
		
		add_action('save_post_page', function($postId) {
			$this->handlePageSavingRequest($postId, $_POST);
		}, 10, 3);

		add_filter('sidebars_widgets', function($sidebars) {
			if (is_page()) {
				$pageId = get_the_ID();
				$activeSidebar = $this->getSidebarByPostId($pageId);
				$activeWidget = $this->getReplaceableByPostId($pageId);

				// None of generated sidebars is applyable
				if (is_null($activeSidebar) || is_null($activeWidget))
					return $sidebars;

				$sidebars[$activeWidget] = $sidebars[$activeSidebar];
			}

			return $sidebars;
		});
	}
}
