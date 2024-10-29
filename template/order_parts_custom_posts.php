<?php if ( isset( $custom_posts_lists, $custom_posts_title_text ) ): ?>
    <div class="list-orders-outer">
		<?php if ( count( $custom_posts_lists ) > 0 ): ?>
			<?php foreach ( $custom_posts_lists as $custom_post ) : ?>
                <div class="list-orders-inner">
                    <h3><?php echo esc_html( $custom_post['label'] ); ?></h3>
					<?php
					$order_target_data = get_option( '_apop_custompost_archive_sort_type' );
					if ( isset( $order_target_data[ $custom_post['slug'] ] ) ) {
						$order_target = $order_target_data[ $custom_post['slug'] ];
					} else {
						$order_target = 2;
					}
					?>
                    <!-- ソートタイプ　-->
                    <ul class="sort-menu-list"
                        data-order_target="<?php echo esc_attr( $order_target ); ?>">
                        <li>
                            <label>
                                <input class="sort_menu" type="radio"
                                       name="_apop_custompost_archive_sort_type[<?php echo esc_attr( $custom_post['slug'] ); ?>]"
                                       value="2"<?php checked( $order_target, 2 ); ?>><?php _e( 'Standard + custom field sort', APOP_DOMAIN ); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input class="sort_menu" type="radio"
                                       name="_apop_custompost_archive_sort_type[<?php echo esc_attr( $custom_post['slug'] ); ?>]"
                                       value="1"<?php checked( $order_target, 1 ); ?>><?php _e( 'Drag sort', APOP_DOMAIN ); ?>
                            </label>
                        </li>
                    </ul>

                    <div class="sort_box">
                        <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                        <ul class="post-order-list drag-sort">
							<?php echo APOP_UI::create_custompost_archive_order_list( $custom_post['slug'] ); ?>
                        </ul>
                    </div>

                    <div class="sort_box">
                        <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                        <div class="enable-box">
                            <h4><?php _e( 'Enabled', APOP_DOMAIN ); ?></h4>
                            <ul class="post-order-list search-normal-sort">
								<?php APOP_UI::create_custompost_archive_normal_list( $custom_post['slug'] ); ?>
                            </ul>
                        </div>
                        <div class="disable-box">
                            <h4><?php _e( 'Disabled', APOP_DOMAIN ); ?></h4>
                            <ul class="disable-normal-list"></ul>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
		<?php else: ?>
            <p class="no-registered-exp"><?php _e( 'No', APOP_DOMAIN ); ?><?php echo esc_html( $custom_posts_title_text . ' ' ); ?><?php _e( 'has been selected.', APOP_DOMAIN ); ?></p>
		<?php endif; ?>
    </div>
<?php endif;