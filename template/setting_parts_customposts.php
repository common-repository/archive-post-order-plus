<?php if ( isset( $custom_post_data, $order_name, $order ) ): ?>
    <dl class="apop-setting-list">
        <dt><?php _e( 'Targets', APOP_DOMAIN ); ?></dt>
        <dd>
            <ul class="order-setting-list">
				<?php foreach ( $custom_post_data as $custom_post_datum => $label ): ?>
					<?php
					$opt_cat    = get_option( $order_name );
					$check_slug = $opt_cat['target_post'][ $custom_post_datum ] ?? '';
					?>
                    <li>
                        <div class="select_cat">
                            <label>
                                <input type="hidden"
                                       name="<?php echo esc_attr( $order_name ); ?>[target_post][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                       value="0">
                                <input class="select_cat_checkbox" type="checkbox"
                                       name="<?php echo esc_attr( $order_name ); ?>[target_post][<?php echo esc_attr( $custom_post_datum ); ?>]"
									<?php checked( $check_slug, 1 ); ?>
                                       value="1">
								<?php echo esc_html( $label ); ?>
                            </label>
                        </div>
						<?php $per_page_data = APOP_UI::create_custom_posts_per_page( get_option( '_apop_per_page' ), $order, $custom_post_datum ); ?>
                        <div class="select-per-page">
                            <ul>
                                <li>
                                    <label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr($order); ?>][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                               value="default"<?php checked( $per_page_data['_per_page'], 'default' ); ?>><?php _e( 'Follow display settings', APOP_DOMAIN ); ?>
                                        （<?php echo esc_html( get_option( 'posts_per_page' ) ); ?>
										<?php _e( 'Posts', APOP_DOMAIN ); ?>）</label>
                                </li>
                                <li><label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr( $order ); ?>][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                               value="all"<?php checked( $per_page_data['_per_page'], 'all' ); ?>><?php _e( 'Follow global settings', APOP_DOMAIN ); ?>
                                    </label>
                                </li>
                                <li><label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr( $order ); ?>][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                               value="-1"<?php checked( $per_page_data['_per_page'], '-1' ); ?>><?php _e( 'All posts', APOP_DOMAIN ); ?>
                                    </label>
                                </li>
                                <li class="set_number_list">
                                    <label><input class="per_page_cat set_number" type="radio"
                                                  name="_apop_per_page[<?php echo esc_attr( $order ); ?>][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                                  value=""<?php echo esc_attr( $per_page_data['_checked'] ); ?>><?php _e( 'Set the number posts', APOP_DOMAIN ); ?>
                                        <input class="per_page_cat_input" type="text"
                                               name="_apop_per_page[<?php echo esc_attr( $order ); ?>][<?php echo esc_attr( $custom_post_datum ); ?>]"
                                               value="<?php echo esc_attr( $per_page_data['_per_page_num'] ); ?>"
                                               required>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </li>
				<?php endforeach; ?>
            </ul>
        </dd>
    </dl>
<?php endif;