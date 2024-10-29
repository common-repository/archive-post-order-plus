<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'APOP' ) ) {

	require_once __DIR__ . '/../util/apop-order-setting.php';
	require_once __DIR__ . '/../util/apop-customfield-select.php';
	require_once __DIR__ . '/../util/apop-customposts.php';
	require_once __DIR__ . '/../util/apop-tax.php';
	require_once __DIR__ . '/../util/apop-output.php';

	class APOP {

		use APOP_ORDER_SETTING, CUSTOMFIELD_SELECT, CUSTOMPOSTS, TAX, APOP_OUTPUT;

		const TEMPLATE_DIR = __DIR__ . '/../template/';

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_pages' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'set_custom_field_ajax' ) );
			add_action( 'wp_ajax_set_custom_field', array( $this, 'set_custom_field' ) );
		}

		public function add_pages() {
			add_menu_page(
				'APO +',
				'APO +',
				'level_8',
				'apop_post_sort',
				array( $this, 'display_setting_page' ),
				''
			);
			//
			add_submenu_page(
				'apop_post_sort', // parent_slug
				'Sort taxonomy', // page_title
				__( 'Display number / target setting', APOP_DOMAIN ), // menu_title
				'administrator', // capability
				'apop_post_sort', // menu_slug
				array( $this, 'display_setting_page' ) // function
			);
			add_submenu_page(
				'apop_post_sort', // parent_slug
				'Select taxonomy', // page_title
				__( 'Sorting', APOP_DOMAIN ), // menu_title
				'administrator', // capability
				'apop_post_sort_setting', // menu_slug
				array( $this, 'show_option_page' ) // function
			);
		}

		public function display_setting_page() {

			$settings = array(
				'_apop_cat_order', //カテゴリー設定
				'_apop_per_page', //１ページ表示件数
				'_apop_tag_order', //タグ設定
				'_apop_tax_order', //カスタムタクソノミー設定
				'_apop_custompost_archive_order', //カスタム投稿設定
			);

			foreach ( $settings as $setting ) {
				$opt = APOP_UI::input_post_filter( $setting, 'array' );
				if ( $opt ) {
					check_admin_referer( 'sh_options' );
					update_option( $setting, $opt );
					require_once self::TEMPLATE_DIR . 'success.php';
				}
			}

			require_once self::TEMPLATE_DIR . 'setting.php';

		}


		public function show_option_page() {

			//最新の投稿表示
			$this->set_new_lists();

			// 検索表示
			$this->set_search_lists();

			// カスタム投稿アーカイブ
			$this->set_custompost_archive_list();

			// タクソノミー
			$this->set_tax_list();

			require_once self::TEMPLATE_DIR . 'order.php';
		}

		//---------------------------------------------------------------------------------
		// 最新の投稿表示
		//---------------------------------------------------------------------------------
		private function set_new_lists() {
			$apop_normal_order       = APOP_UI::input_post_filter( '_apop_normal_order', 'str' );
			$apop_normal_order_param = APOP_UI::input_post_filter( '_apop_normal_order_param', 'array' );
			$apop_post_normal        = APOP_UI::input_post_filter( '_apop_post_normal', 'array' );

			if ( $apop_normal_order ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_normal_order', $apop_normal_order );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//表示順設定
			if ( $apop_normal_order_param ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_normal_order_param', $apop_normal_order_param );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カスタム表示順設定
			if ( $apop_post_normal ) {
				check_admin_referer( 'sh_options' );
				$this->update_search_normal_sort( $apop_post_normal, 'normal' );
				require_once self::TEMPLATE_DIR . 'success.php';
			}
		}

		//---------------------------------------------------------------------------------
		// 検索結果
		//---------------------------------------------------------------------------------
		private function set_search_lists() {
			$apop_search_order       = APOP_UI::input_post_filter( '_apop_search_order', 'str' );
			$apop_search_order_param = APOP_UI::input_post_filter( '_apop_search_order_param', 'array' );
			$apop_post_search        = APOP_UI::input_post_filter( '_apop_post_search', 'array' );

			if ( $apop_search_order ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_search_order', $apop_search_order );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//表示順設定
			if ( $apop_search_order_param ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_search_order_param', $apop_search_order_param );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カスタム表示順設定
			if ( $apop_post_search ) {
				check_admin_referer( 'sh_options' );
				$this->update_search_normal_sort( $apop_post_search, 'search' );
				require_once self::TEMPLATE_DIR . 'success.php';
			}
		}

		//検索結果と最近の投稿の表示順の保存
		private function update_search_normal_sort( $apop_post_search_normal, $key ) {
			$posts_sort = $apop_post_search_normal['post_sort'];
			foreach ( $posts_sort as $post_id => $sort ) {
				update_post_meta( $post_id, '_apop_post_' . $key, $sort );
			}
		}

		//---------------------------------------------------------------------------------
		// カスタム投稿アーカイブ
		//---------------------------------------------------------------------------------
		private function set_custompost_archive_list() {
			$custompost_archive_sort_type        = APOP_UI::input_post_filter( '_apop_custompost_archive_sort_type', 'array' );
			$apop_custompost_archive_order_param = APOP_UI::input_post_filter( '_apop_custompost_archive_order_param', 'array' );
			$apop_custompost_archive             = APOP_UI::input_post_filter( '_apop_customposts', 'array' );

			//カスタム投稿アーカイブのソートタイプ設定
			if ( $custompost_archive_sort_type ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_custompost_archive_sort_type', $custompost_archive_sort_type );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カスタム投稿アーカイブの標準ソートパラメータ
			if ( $apop_custompost_archive_order_param ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_custompost_archive_order_param', $apop_custompost_archive_order_param );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カスタム投稿アーカイブの投稿表示順設定
			if ( $apop_custompost_archive ) {
				check_admin_referer( 'sh_options' );
				$this->update_custom_post_sort( $apop_custompost_archive );
				require_once self::TEMPLATE_DIR . 'success.php';
			}
		}

		//カスタム投稿アーカイブ表示順の保存
		private function update_custom_post_sort( $post_value ) {
			foreach ( $post_value as $custom_post_type => $posts ) {
				foreach ( $posts as $post_id => $sort ) {
					$sort_key = '_apop_customposts_' . $custom_post_type;
					update_post_meta( $post_id, $sort_key, $sort );
				}
			}
		}

		//---------------------------------------------------------------------------------
		// タクソノミー
		//---------------------------------------------------------------------------------
		private function set_tax_list() {
			$tax_sort_type        = APOP_UI::input_post_filter( '_apop_tax_sort_type', 'array' );
			$apop_tax_order_param = APOP_UI::input_post_filter( '_apop_tax_order_param', 'array' );
			$apop_post_category   = APOP_UI::input_post_filter( '_apop_post_category', 'array' );
			$apop_post_post_tag   = APOP_UI::input_post_filter( '_apop_post_post_tag', 'array' );
			$apop_post_tax        = APOP_UI::input_post_filter( '_apop_post_tax', 'array' );

			//カテゴリーのソートタイプ設定
			if ( $tax_sort_type ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_tax_sort_type', $tax_sort_type );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カテゴリーの標準ソートパラメータ
			if ( $apop_tax_order_param ) {
				check_admin_referer( 'sh_options' );
				update_option( '_apop_tax_order_param', $apop_tax_order_param );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カテゴリーの投稿表示順設定
			if ( $apop_post_category ) {
				check_admin_referer( 'sh_options' );
				$this->update_post_sort( 'category', $apop_post_category );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//タグの投稿表示順設定
			if ( $apop_post_post_tag ) {
				check_admin_referer( 'sh_options' );
				$this->update_post_sort( 'post_tag', $apop_post_post_tag );
				require_once self::TEMPLATE_DIR . 'success.php';
			}

			//カスタム分類の投稿表示順設定
			if ( $apop_post_tax ) {
				check_admin_referer( 'sh_options' );
				$this->update_post_sort( 'tax', $apop_post_tax );
				require_once self::TEMPLATE_DIR . 'success.php';
			}
		}

		//タクソノミーの表示順の保存
		private function update_post_sort( $target, $post_value ) {
			$posts_sort = $post_value['post_sort'];
			foreach ( $posts_sort as $cat_id => $posts ) {
				$sort_key = '_apop_post_' . $target . '_' . $cat_id;
				foreach ( $posts as $post_id => $sort ) {
					update_post_meta( $post_id, $sort_key, $sort );
				}
			}
		}

	}

}

