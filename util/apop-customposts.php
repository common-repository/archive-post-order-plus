<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! trait_exists( 'CUSTOMPOSTS' ) ) {

	/**
	 * カスタム投稿アーカイブのソート
	 * Trait CUSTOMPOSTS
	 */
	trait CUSTOMPOSTS {

		public static function get_enable_custompost_archive(): array {
			$opt = get_option( '_apop_custompost_archive_order' );
			if ( ! isset( $opt['target_post'] ) ) {
				return array();
			}
			$custom_post_list = array();
			foreach ( $opt['target_post'] as $target => $check ) {
				if ( $check == '1' ) {
					$custom_post_list[] = [
						'label' => get_post_type_object( $target )->label,
						'slug'  => $target,
					];
				}
			}

			return $custom_post_list;
		}

		public static function create_custompost_archive_order_list( $slug ): string {
			$args = array(
				'posts_per_page' => - 1,
				'post_type'      => array( $slug ),
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
			);
			self::create_sort_post_list_meta_query( $args, '_apop_customposts_' . $slug );
			$return_data = get_posts( $args );
			$list        = array();
			foreach ( $return_data as $key => $target_post ) {
				$sort_num = $key + 1;
				$no_order = self::is_sort_custompost_archive_registered( $target_post->ID, $slug ) ? '' : ' no-order';
				$list[]   = '
				<li class="product-list' . esc_attr( $no_order ) . '">' . '<span class="sort-num-label">' . esc_attr( $sort_num ) . '</span>' . get_the_title( $target_post->ID ) . '
				<input type="hidden" class="list-order" name="_apop_customposts[' . esc_attr( $slug ) . '][' . esc_attr( $target_post->ID ) . ']" value="' . esc_attr( $sort_num ) . '">
				</li>';
			}

			return implode( PHP_EOL, $list );
		}


		private static function is_sort_custompost_archive_registered( $id, $slug ): bool {
			$post_meta = get_post_meta( $id, '_apop_customposts_' . $slug, true );
			if ( ! empty( $post_meta ) ) {
				return true;
			}

			return false;
		}

		public static function create_custompost_archive_normal_list( $slug ) {
			$name_keys        = array(
				'name_key'       => 'apop_custompost_archive_order_param[' . $slug . ']',
				'get_option_key' => '_apop_custompost_archive_order_param',
			);
			$name_key         = $name_keys['name_key'];
			$get_option_key   = $name_keys['get_option_key'];
			$order_param_base = get_option( $get_option_key );
			$order_param      = '';
			if ( isset( $order_param_base[ $slug ] ) ) {
				$order_param = $order_param_base[ $slug ];
			}
			self::create_normal_sort_list( $name_key, $order_param );
		}

		private function set_custompost_orderby( $query, $post_type ) {
			$sort_type = get_option( '_apop_custompost_archive_sort_type' )[ $post_type ];
			if ( $sort_type == 1 ) {
				$sort_meta_key = self::set_custompost_sort_meta_key( $post_type );
				if ( is_null( $sort_meta_key ) ) {
					$this->set_search_normal_orderby( $query, 'custompost_archive' );
				} else {
					$query->set( 'meta_query', self::get_all_post_args( $sort_meta_key ) );
					$query->set( 'orderby', array( 'meta_value' => 'ASC', ) );
				}
			} else {
				$meta_orderby = self::set_custompost_custom_field_orderby( $post_type );
				if ( isset( $meta_orderby['meta_query'] ) ) {
					$query->set( 'meta_query', $meta_orderby['meta_query'] );
				}
				if ( isset( $meta_orderby['orderby'] ) ) {
					$query->set( 'orderby', $meta_orderby['orderby'] );
				}
			}
			$per_page_option_data = self::set_custompost_per_page( $post_type );
			$query->set( 'posts_per_page', $per_page_option_data );
		}

		private static function set_custompost_sort_meta_key( $post_type ) {
			$order_settings = get_option( '_apop_custompost_archive_order' );
			if ( isset( $order_settings['target_post'][ $post_type ] ) && $order_settings['target_post'][ $post_type ] == 1 ) {
				return '_apop_customposts_' . $post_type;
			}

			return null;
		}

		private static function set_custompost_custom_field_orderby( $post_type ): array {
			$order_param = get_option( '_apop_custompost_archive_order_param' );
			if ( isset( $order_param[ $post_type ] ) ) {
				return self::set_custom_field_orderby( $order_param[ $post_type ] );
			}
		}

		private static function set_custompost_per_page( $post_type ) {
			$per_page_option = get_option( '_apop_per_page' );
			if ( ! $per_page_option ) {
				return;
			}
			
			if( ! isset($per_page_option['custompost_archive'] ) ){
				return;
			}

			$per_page_option_data = $per_page_option['custompost_archive'][ $post_type ];
			if ( ! isset( $per_page_option_data ) ) {
				return;
			}

			if ( $per_page_option_data == 'default' ) {
				return;
			}

			if ( $per_page_option_data == 'all' ) {
				if ( $per_page_option['search'] == 'default' ) {
					return;
				}
				$per_page_option_data = $per_page_option['search'];
			}

			return $per_page_option_data;
		}

	}
}