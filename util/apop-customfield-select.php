<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
if ( ! trait_exists( 'CUSTOMFIELD_SELECT' ) ) {

	trait CUSTOMFIELD_SELECT {

		public function set_custom_field_ajax() {
			$handle = 'custom_field_ajax';
			wp_register_script( $handle, APOP_PLUGIN_URL . 'js/custom_field.js', [ 'jquery' ], '', true );
			$localize = [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'set_custom_field',
			];
			wp_localize_script( $handle, 'localize', $localize );
			wp_enqueue_script( $handle );
		}

		public function set_custom_field() {
			$param = filter_input( INPUT_GET, 'param', FILTER_SANITIZE_STRING );
			echo json_encode( $this->get_custom_fields_by_param( $param ) );
			die();
		}

		private function get_custom_fields_by_param( $param ) {
			global $wpdb;
			$stmnt = "SELECT DISTINCT meta_key AS value, meta_key as label FROM $wpdb->postmeta WHERE meta_key LIKE %s AND meta_key NOT LIKE %s";

			return $wpdb->get_results( $wpdb->prepare( $stmnt, $wpdb->esc_like( $param ) . '%', $wpdb->esc_like( '_%' ) ) );
		}

	}
}