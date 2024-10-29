<?php
/*
Plugin Name: Archive Post Order Plus
Plugin URI: https://develop.n-k-y.net/wordpress/wp_plugin/apop/
Author: NBK45
Author URI: https://develop.n-k-y.net
Description: Archive Post Order Plus は「最新の投稿」「検索結果」「カテゴリー」「タグ」「カスタム分類」の投稿記事の表示順をドラッグで並べ替えて設定するプラグインです。
Version: 1.2.3
License: GPLv2
Text Domain: ArchivePostOrderPlus
Domain Path: /languages
 */

/*  Copyright 2021 Nobuhiro Kimura (email : big-me@n-k-y.net)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'APOP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APOP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'APOP_CUSTOM_FIELD_PREFIX', '_apop_postorder_' );
define( 'APOP_DOMAIN', 'ArchivePostOrderPlus' );
load_plugin_textdomain( APOP_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

//メイン処理のクラスをインスタンス化
require_once __DIR__ . '/class/class.apop.order.php';
require_once __DIR__ . '/class/class.apop.apop_ui.php';
require_once __DIR__ . '/class/class.apop.apop_post.php';

$APOP = new APOP;
new APOP_POST;

//CSS, JSの読み込み
add_action( 'admin_enqueue_scripts', 'apop_register_my_styles' );
function apop_register_my_styles() {
	wp_enqueue_style( 'jq_ui_css', APOP_PLUGIN_URL . 'css/jquery-ui.css' );
	wp_enqueue_style( 'hrc_post_style', APOP_PLUGIN_URL . 'css/apop-style.css' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_enqueue_script( 'post-sort-cat-order_js', APOP_PLUGIN_URL . 'js/apop-style.js' );
	if ( wp_is_mobile() ) {
		wp_enqueue_script( 'jquery-touch-punch' );
	}
}

$APOP->set_query();
