<?php

class JTSidebarGeneratorDebugHelper {

	public static function debug($data = array(), $title = ''){
		if( is_array($data) ){
			array_walk_recursive( $data, 'self::debugFilter' );
		}
		if( !empty($title) ){
			echo '<h3>'. $title .'</h3>';
		}
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	public static function debugFilter(&$data){
		$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	}
}
