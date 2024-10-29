<?php
$submit_type = APOP_UI::input_post_filter( 'apop_submit_type', 'str' );
?>
<div class="post-setting-box <?php _e( 'apop-lang-en', APOP_DOMAIN ); ?>">
    <h2><?php _e( 'Sorting', APOP_DOMAIN ); ?></h2>
    <nav class="post-order-nav">
        <ul>
            <li class="en"><?php _e( 'Your latest posts', APOP_DOMAIN ); ?></li>
            <li><?php _e( 'Search', APOP_DOMAIN ); ?></li>
            <li><?php _e( 'Category', APOP_DOMAIN ); ?></li>
            <li><?php _e( 'Tag', APOP_DOMAIN ); ?></li>
            <li><?php _e( 'Custom taxonomy', APOP_DOMAIN ); ?></li>
            <li><?php _e( 'Custom posts', APOP_DOMAIN ); ?></li>
        </ul>
    </nav>
    <form action="" method="post">
        <div class="post-order-box-outer">
			<?php wp_nonce_field( 'sh_options' ); ?>
            <input id="apop_submit_type" type="hidden" name="apop_submit_type"
                   value="<?php echo esc_attr( $submit_type ); ?>">
            <!-- 最新の投稿の設定 -->
            <div class="post-order-box">
                <div class="list-orders-outer">
                    <div class="list-orders-inner">
                        <h3><?php _e( 'Your latest posts / Settings - Reading Settings', APOP_DOMAIN ); ?></h3>
						<?php
						$order_target_type = '_apop_normal_order';
						$order_target      = APOP_UI::get_order_type( $order_target_type );
						?>
						<?php include APOP_PLUGIN_PATH . 'template/order_parts_menu.php'; ?>
                        <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                        <div class="sort_box">
                            <div class="enable-box">
                                <h4><?php _e( 'Enabled', APOP_DOMAIN ); ?></h4>
                                <ul class="post-order-list search-normal-sort">
									<?php APOP_UI::create_search_normal_list( 'normal' ); ?>
                                </ul>
                            </div>
                            <div class="disable-box">
                                <h4><?php _e( 'Disabled', APOP_DOMAIN ); ?></h4>
                                <ul class="disable-normal-list"></ul>
                            </div>
                        </div>
                        <div class="sort_box">
                            <ul class="post-order-list drag-sort">
								<?php APOP_UI::get_all_search_normal_posts( 'normal' ); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 検索の設定　-->
            <div class="post-order-box">
                <div class="list-orders-outer">
                    <div class="list-orders-inner">
                        <h3><?php _e( 'Search', APOP_DOMAIN ); ?></h3>
						<?php
						$order_target_type = '_apop_search_order';
						$order_target      = APOP_UI::get_order_type( $order_target_type );
						?>
						<?php include APOP_PLUGIN_PATH . 'template/order_parts_menu.php'; ?>
                        <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                        <div class="sort_box">
                            <div class="enable-box">
                                <h4><?php _e( 'Enabled', APOP_DOMAIN ); ?></h4>
                                <ul class="post-order-list search-normal-sort">
									<?php APOP_UI::create_search_normal_list( 'search' ); ?>
                                </ul>
                            </div>
                            <div class="disable-box">
                                <h4><?php _e( 'Disabled', APOP_DOMAIN ); ?></h4>
                                <ul class="disable-normal-list"></ul>
                            </div>
                        </div>
                        <div class="sort_box">
                            <ul class="post-order-list drag-sort">
								<?php APOP_UI::get_all_search_normal_posts( 'search' ); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- カテゴリーの設定　-->
            <div class="post-order-box">
                <div class="tax_sort_box">
					<?php
					$tax_lists      = array( 'category' => APOP_UI::get_cat_tag_list( 'cat', 'category' ) );
					$tax_title_text = __( 'Category', APOP_DOMAIN );
					include APOP_PLUGIN_PATH . 'template/order_parts_taxonomy.php';
					?>
                </div>
            </div>
            <!-- タグの設定　-->
            <div class="post-order-box">
                <div class="tax_sort_box">
					<?php
					$tax_lists      = array( 'tag_id' => APOP_UI::get_cat_tag_list( 'tag', 'post_tag' ) );
					$tax_title_text = __( 'Tag', APOP_DOMAIN );
					include APOP_PLUGIN_PATH . 'template/order_parts_taxonomy.php';
					?>
                </div>
            </div>
            <!-- カスタム分類の設定　-->
            <div class="post-order-box">
                <div class="tax_sort_box">
					<?php
					$tax_lists      = array( 'taxonomy' => APOP_UI::get_cat_tag_list( 'tax', 'taxonomy' ) );
					$tax_title_text = __( 'Custom taxonomy', APOP_DOMAIN );
					include APOP_PLUGIN_PATH . 'template/order_parts_taxonomy.php'; ?>
                </div>
            </div>
            <!-- カスタム投稿アーカイブの設定　-->
            <div class="post-order-box">
                <div class="tax_sort_box">
					<?php
					$custom_posts_lists      = APOP_UI::get_enable_custompost_archive();
					$custom_posts_title_text = __( 'Custom posts', APOP_DOMAIN );
					include APOP_PLUGIN_PATH . 'template/order_parts_custom_posts.php';
					?>
                </div>
            </div>
        </div>
        <p class="apop-submit"><input type="submit" name="submit" class="button-primary"
                                      value="<?php _e( 'Save changes', APOP_DOMAIN ); ?>"/></p>
    </form>
</div>