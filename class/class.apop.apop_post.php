<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'APOP_POST' ) ) {

	class APOP_POST {

		const TEMPLATE_DIR = __DIR__ . '/../template/';

		private $order_field = array();
		private $custom_field_type = array();
		private $name_keys = array();
		private $order_param_keys = array(
			'custom_field',
			'custom_field_2',
			'custom_field_3',
			'custom_field_4',
		);
		private $labels;

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_meta_fields' ) );
			add_action( 'save_post', array( $this, 'save_meta_fields' ) );
		}

		//メタボックスを追加
		public function add_meta_fields() {
			$target_posts = $this->set_post_types();
			foreach ( $target_posts as $target_post ) {
				add_meta_box( 'apo_custom_fields', __( 'APO + custom field settings', APOP_DOMAIN ), array(
					$this,
					'insert_meta_fields'
				), $target_post, 'normal' );
			}
		}

		public function insert_meta_fields() {
			//最新の投稿ソート
			if ( get_option( '_apop_normal_order' ) == '1' ) {
				$this->create_order_field_data( get_option( '_apop_normal_order_param' ), 'normal' );
			}
			//検索ソート
			if ( get_option( '_apop_search_order' ) == '1' ) {
				$this->create_order_field_data( get_option( '_apop_search_order_param' ), 'search' );
			}

			//カスタム投稿アーカイブソート
			$this->create_order_custompost_data( get_option( '_apop_custompost_archive_order_param' ) );

			//カテゴリー、タグ、カスタム分類
			$this->create_order_tax_field_data( get_option( '_apop_tax_order_param' ) );

			$this->labels = array(
				'normal'     => __( 'Your latest posts', APOP_DOMAIN ),
				'search'     => __( 'Search', APOP_DOMAIN ),
				'tax'        => __( 'Category, Tag, Custom Taxonomy', APOP_DOMAIN ),
				'custompost' => __( 'Custom posts archive', APOP_DOMAIN ),
			);

			require_once self::TEMPLATE_DIR . 'setting_post_custom_field.php';

		}

		private function set_post_types() {
			$custom_posts = array_values( get_post_types( array( 'public' => true, '_builtin' => false ) ) );

			return array_merge( array( 'page', 'post', ), $custom_posts );
		}

		private function create_order_field_data( $param, $key, $update = false ) {
			foreach ( $this->order_param_keys as $order_param_key ) {
				if ( isset( $param[ $order_param_key ]['field']['meta_key'] ) &&
				     ! empty( $param[ $order_param_key ]['field']['meta_key'] ) ) {
					if ( $update ) {
						$this->name_keys[] = $param[ $order_param_key ]['field']['meta_key'];
					} else {
						$this->custom_field_type[ $key ][] = $param[ $order_param_key ]['field']['custom_field_type'];
						$this->order_field[ $key ][]       = $param[ $order_param_key ]['field']['meta_key'];
					}
				}
			}
		}

		private function create_order_custompost_data( $param, $update = false ) {
			global $post_type;
			//カスタム投稿タイプでないときは処理を抜ける
			if ( $post_type == 'page' || $post_type == 'post' ) {
				return;
			}

			$archive_settings = get_option( '_apop_custompost_archive_sort_type' );
			if ( $archive_settings && array_key_exists( $post_type, $archive_settings )
			     && $archive_settings[ $post_type ] == 2 ) {
				foreach ( $this->order_param_keys as $order_param_key ) {
					if ( isset( $param[ $post_type ][ $order_param_key ]['field']['meta_key'] ) &&
					     ! empty( $param[ $post_type ][ $order_param_key ]['field']['meta_key'] ) ) {
						if ( $update ) {
							$this->name_keys[] = $param[ $post_type ][ $order_param_key ]['field']['meta_key'];
						} else {
							$this->custom_field_type['custompost'][] = $param[ $post_type ][ $order_param_key ]['field']['custom_field_type'];
							$this->order_field['custompost'][]       = $param[ $post_type ][ $order_param_key ]['field']['meta_key'];
						}
					}
				}
			}
		}

		private function create_order_tax_field_data( $param, $update = false ) {
			$term_ids   = $this->create_post_term_ids();
			$sort_types = get_option( '_apop_tax_sort_type' );
			foreach ( $term_ids as $term_id ) {
				if ( ! isset( $sort_types[ $term_id ] ) ) {
					continue;
				}
				if ( $sort_types[ $term_id ] == '1' ) {
					break;
				}
				foreach ( $this->order_param_keys as $order_param_key ) {
					if ( isset( $param[ $term_id ][ $order_param_key ]['field']['meta_key'] ) &&
					     ! empty( $param[ $term_id ][ $order_param_key ]['field']['meta_key'] ) ) {
						if ( $update ) {
							$this->name_keys[] = $param[ $term_id ][ $order_param_key ]['field']['meta_key'];
						} else {
							$this->custom_field_type['tax'][] = $param[ $term_id ][ $order_param_key ]['field']['custom_field_type'];
							$this->order_field['tax'][]       = $param[ $term_id ][ $order_param_key ]['field']['meta_key'];
						}
					}
				}
			}
		}

		private function create_post_term_ids() {
			global $post;
			$terms          = array();
			$term_ids       = array();
			$taxonomy_slugs = array_keys( get_the_taxonomies() );
			foreach ( $taxonomy_slugs as $taxonomy_slug ) {
				$terms[] = get_the_terms( $post->ID, $taxonomy_slug );

			}
			foreach ( $terms as $term ) {
				foreach ( $term as $tax ) {
					$term_ids[] = $tax->term_id;
				}
			}

			return $term_ids;
		}

		private function get_custom_field_data( $custom_filed_name, $prefix ) {
			global $post;

			return get_post_meta( $post->ID, $prefix . $custom_filed_name, true );
		}

		// カスタムフィールドの値を保存
		public function save_meta_fields( $post_id ) {
			$this->get_update_meta_fields();
			if ( count( $this->name_keys ) > 0 ) {
				foreach ( $this->name_keys as $name_key ) {
					$save_key = APOP_CUSTOM_FIELD_PREFIX . $name_key;
					update_post_meta( $post_id, $save_key, APOP_UI::input_post_filter( $save_key, 'str' ) );
				}
			}
		}

		private function get_update_meta_fields() {
			//最新の投稿ソート
			$this->create_order_field_data( get_option( '_apop_normal_order_param' ), 'normal', true );
			//検索ソート
			$this->create_order_field_data( get_option( '_apop_search_order_param' ), 'search', true );
			//カスタム投稿アーカイブ
			$this->create_order_custompost_data( get_option( '_apop_custompost_archive_order_param' ), true );
			//カテゴリー、タグ、カスタム分類
			$this->create_order_tax_field_data( get_option( '_apop_tax_order_param' ), true );
		}

	}
}
