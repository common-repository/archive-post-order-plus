<?php

// WP_UNINSTALL_PLUGINが定義されているかチェック
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

//最新の投稿と検索の表示件数、ソート対象設定等を削除
$param_settings = array(
	'_apop_normal_order_param',
	'_apop_search_order_param',
	'_apop_normal_order',
	'_apop_search_order',
	'_apop_tax_order_param',
	'_apop_tax_sort_type',
	'_apop_cat_order',
	'_apop_tag_order',
	'_apop_tax_order',
	'_apop_custompost_archive_order_param',
	'_apop_custompost_archive_sort_type',
	'_apop_custompost_archive_order',
	'_apop_per_page',
);
foreach ( $param_settings as $param_setting ) {
	delete_option( $param_setting );
}

global $wpdb;

//最新の投稿と検索用のカスタム表示順を削除
$option_orders = array(
	"'" . '_apop_post_normal' . "'",
	"'" . '_apop_post_search' . "'",
);
$stmnt         = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key IN(' . implode( ',', $option_orders ) . ')';
$wpdb->query( $stmnt );

//タクソノミー設定を削除(WEHRE LIKE 'XXX%')
$tax_order_settings = array(
	'_apop_post_category_',
	'_apop_post_post_tag_',
	'_apop_post_tax_',
	'_apop_customposts_',
	'_apop_postorder_'
);
foreach ( $tax_order_settings as $tax_order_setting ) {
	$stmnt = 'DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key LIKE ' . "'" . $tax_order_setting . "%'";
	$wpdb->query( $stmnt );
}
