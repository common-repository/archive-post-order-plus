<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! trait_exists( 'APOP_ORDER_SETTING' ) ) {

	trait APOP_ORDER_SETTING {

		public function set_query() {
			add_action( 'pre_get_posts', array( $this, 'apop_posts_per_page' ), 1 );
		}

		/**
		 * ソートのクエリを生成
		 *
		 * @param $query
		 */
		public function apop_posts_per_page( $query ) {

			if ( is_admin() || ! $query->is_main_query() ) {
				return;
			}

			if ( is_home() ) {
				$this->set_search_normal_orderby( $query, 'normal' );
			}

			if ( is_search() ) {
				$this->set_search_normal_orderby( $query, 'search' );
			}

			if ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( $post_type ) {
					$this->set_custompost_orderby( $query, $post_type );
				}
			}

			if ( is_category() ) {
				$cat = get_category_by_slug( $query->query_vars['category_name'] );
				if ( isset( $cat->term_id ) ) {
					$this->set_orderby( $query, $cat->term_id, 'category', 'cat' );
				}
			}

			if ( is_tag() ) {
				$tag = get_term_by( 'slug', $query->query_vars['tag'], 'post_tag' );
				if ( isset( $tag->term_id ) ) {
					$this->set_orderby( $query, $tag->term_id, 'post_tag', 'tag' );
				}
			}

			if ( is_tax() ) {
				if ( get_queried_object_id() ) {
					$this->set_orderby( $query, get_queried_object_id(), 'tax', 'tax' );
				}
			}
		}

		//-----------------------------------------------------------
		// 最近の投稿と検索のソートのクエリ生成
		//-----------------------------------------------------------
		private function set_search_normal_orderby( $query, $type ) {
			$apop_order = get_option( '_apop_' . $type . '_order' ) ?? 1;
			if ( $apop_order == 2 ) {
				$query->set( 'orderby', array( 'meta_value_num' => 'ASC' ) );
				$query->set( 'meta_query', self::get_all_post_args( '_apop_post_' . $type ) );
			} else {
				$apop_order_param = get_option( '_apop_' . $type . '_order_param' );
				$meta_orderby     = self::set_custom_field_orderby( $apop_order_param );
				if ( isset( $meta_orderby['meta_query'] ) ) {
					$query->set( 'meta_query', $meta_orderby['meta_query'] );
				}
				if ( isset( $meta_orderby['orderby'] ) ) {
					$query->set( 'orderby', $meta_orderby['orderby'] );
				}
			}
			$per_page_option_data = self::set_per_page( 'search' );
			$query->set( 'posts_per_page', $per_page_option_data );
		}

		//-----------------------------------------------------------
		//標準ソート＋カスタムフィールドソートのカスタムフィールドデータ取得
		//-----------------------------------------------------------
		private static function set_custom_field_orderby( $apop_order_param ): array {
			if ( ! $apop_order_param ) {
				return array();
			}
			$sort_param = array( 1 => 'ASC', 2 => 'DESC' );
			$meta_query = array( 'relation' => 'AND' );
			$orderby    = null;
			foreach ( $apop_order_param as $order_col => $orders ) {
				if ( $orders['use'] == 1 ) {
					if ( strpos( $order_col, 'custom_field' ) !== false ) {
						list( $sort_meta_key, $type ) = self::set_custom_filed_sort( $orderby, $order_col, $orders, $sort_param );
						$meta_query[] = self::get_all_post_args( $sort_meta_key, $type );
					} else {
						if ( isset( $orders['sort'] ) ) {
							$orderby[ $order_col ] = $sort_param[ $orders['sort'] ];
						}
					}
				}
			}

			return array(
				'meta_query' => $meta_query,
				'orderby'    => $orderby
			);
		}

		private static function set_custom_filed_sort( &$orderby, $order_col, $orders, $sort_param ): array {
			if ( strpos( $order_col, 'custom_field' ) !== false ) {
				if ( $orders['field']['custom_field_type'] == 2 ) {
					$meta_key = APOP_CUSTOM_FIELD_PREFIX . $orders['field']['meta_key'];
				} else {
					$meta_key = $orders['field']['meta_key'];
				}
				$type = 'CHAR';
				if ( $orders['field']['value_type'] == 'meta_value_num' ) {
					$type = 'NUMERIC';
				} elseif ( $orders['field']['value_type'] == 'meta_value' ) {
					$type = 'CHAR';
				} elseif ( $orders['field']['value_type'] == 'meta_date' ) {
					$type = 'DATE';
				}
				if ( isset( $orders['sort'] ) ) {
					$orderby[ $meta_key ] = $sort_param[ $orders['sort'] ];
				}

				return array( $meta_key, $type );
			}
		}

		//-----------------------------------------------------------
		// ソートキーが無くともデータを抽出するようEXISTSとNOT EXISTSをリレーションする
		//-----------------------------------------------------------
		private static function get_all_post_args( $sort_meta_key, $type = 'numeric' ): array {
			return array(
				'relation' => 'OR',
				array(
					'key'     => $sort_meta_key,
					'compare' => 'EXISTS',
					'type'    => $type,
				),
				array(
					'key'     => $sort_meta_key,
					'compare' => 'NOT EXISTS',
					'type'    => $type,
				),
			);
		}

		//-----------------------------------------------------------
		// 1ページ表示件数の取得
		//-----------------------------------------------------------
		private static function set_per_page( $target, $id = null ) {
			$per_page_option = get_option( '_apop_per_page' );

			if ( ! $per_page_option ) {
				return;
			}

			if ( is_null( $id ) ) {
				$per_page_option_data = $per_page_option[ $target ];
			} else {
				$per_page_target      = $target == 'tax' ? 'taxonomy' : $target;
				$per_page_option_data = $per_page_option[ $per_page_target ][ $id ];
			}

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
