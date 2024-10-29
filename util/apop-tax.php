<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! trait_exists( 'TAX' ) ) {
	/**
	 * タクソノミーのソート
	 * Trait TAX
	 */
	trait TAX {
		private function set_orderby( $query, $id, $target, $order_key ) {
			$sort_type = self::get_tax_sort_type( $id );
			if ( $sort_type == 1 ) {
				$sort_meta_key = self::set_tax_sort_meta_key( $id, $target, $order_key );
				if ( is_null( $sort_meta_key ) ) {
					$this->set_search_normal_orderby( $query, 'normal' );
				} else {
					$query->set( 'meta_query', self::get_all_post_args( $sort_meta_key ) );
					$query->set( 'orderby', array( 'meta_value' => 'ASC', ) );
				}
			} else {
				$meta_orderby = self::set_tax_custom_field_orderby( $id );
				if ( isset( $meta_orderby['meta_query'] ) ) {
					$query->set( 'meta_query', $meta_orderby['meta_query'] );
				}
				if ( isset( $meta_orderby['orderby'] ) ) {
					$query->set( 'orderby', $meta_orderby['orderby'] );
				}
			}
			$per_page_option_data = self::set_per_page( self::get_per_page_tag( $target ), $id );
			$query->set( 'posts_per_page', $per_page_option_data );
		}

		private static function get_tax_sort_type( $id ) {
			$sort_type = get_option( '_apop_tax_sort_type' );

			return $sort_type[ $id ] ?? 1;
		}

		private static function set_tax_sort_meta_key( $id, $target, $order_key ) {
			$order_settings = get_option( '_apop_' . $order_key . '_order' );
			if ( isset( $order_settings['target_cat'][ $id ] ) && $order_settings['target_cat'][ $id ] == 1 ) {
				return '_apop_post_' . $target . '_' . $id;
			}

			return null;
		}

		private static function set_tax_custom_field_orderby( $id ): array {
			$tax_order_param = get_option( '_apop_tax_order_param' );
			if ( isset( $tax_order_param[ $id ] ) ) {
				return self::set_custom_field_orderby( $tax_order_param[ $id ] );
			}
		}

		private static function get_per_page_tag( $target ) {
			if ( $target == 'post_tag' ) {
				return 'tag';
			}

			return $target;
		}
	}
}