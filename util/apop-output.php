<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! trait_exists( 'APOP_OUTPUT' ) ) {
	/**
	 * TODO: 出力用のコード
	 * Trait APOP_OUTPUT
	 */
	trait APOP_OUTPUT {
		/**
		 *
		 * 1ページ表示件数を取得する
		 * APOP::per_page(ID, [TARGET]);
		 * [TARGET]
		 *  cat
		 *  tag
		 *  tax
		 *
		 * @param string $target
		 * @param null $id
		 *
		 * @return array
		 */
		public static function per_page( $id = null, string $target = 'search' ) {
			if ( $target == 'cat' ) {
				$target = 'category';
			}

			return array( 'posts_per_page' => self::set_per_page( $target, $id ) );
		}


		/**
		 * 最新の投稿ページのソートを取得する
		 * APOP::orderby_normal()
		 * @return array|null
		 */
		public static function orderby_normal() {
			$apop_order = get_option( '_apop_normal_order' ) ?? 1;
			if ( $apop_order == 2 ) {
				return array(
					'meta_query' => self::get_all_post_args( '_apop_normal_order' ),
					'orderby'    => array( 'meta_value_num' => 'ASC' ),
				);
			} else {
				$apop_order_param = get_option( '_apop_normal_order_param' );
				$meta_orderby     = self::set_custom_field_orderby( $apop_order_param );
				if ( is_null( $meta_orderby['meta_query'] ) ) {
					unset( $meta_orderby['meta_query'] );
				}
				if ( is_null( $meta_orderby['orderby'] ) ) {
					unset( $meta_orderby['orderby'] );
				}

				return $meta_orderby;
			}

			return;
		}

		/**
		 * カテゴリー、タグ、カスタム分類のソートを取得する
		 * APOP::orderby_tax([ID, TARGET], );
		 * [TARGET]
		 *  cat
		 *  tag
		 *  tax
		 *
		 * @param $id
		 * @param $target
		 *
		 * @return array
		 */
		public static function orderby_tax( $id, $target ) {
			$sort_type = self::get_tax_sort_type( $id );
			if ( $sort_type == 1 ) {
				$order_key = $target;
				if ( $target == 'tag' ) {
					$target == 'post_tag';
				} elseif ( $target = 'cat' ) {
					$target == 'category';
				}
				$sort_meta_key = self::set_tax_sort_meta_key( $id, $target, $order_key );
				if ( is_null( $sort_meta_key ) ) {
					return self::orderby_normal();
				} else {
					return array(
						'meta_query' => self::get_all_post_args( $sort_meta_key ),
						'orderby'    => array( 'meta_value_num' => 'ASC' ),
					);
				}
			} else {
				return self::set_tax_custom_field_orderby( $id );
			}

			return;
		}

	}
}