<?php if ( isset( $tax_data, $order_name, $order_tax ) ): ?>
    <dl class="apop-setting-list">
        <dt><?php _e('Targets', APOP_DOMAIN); ?></dt>
        <dd>
            <ul class="order-setting-list">
				<?php foreach ( $tax_data as $tax_datum ): ?>
					<?php
					$opt_cat    = get_option( $order_name );
					$check_slug = $opt_cat['target_cat'][ $tax_datum->term_id ] ?? '';
					?>
                    <li>
                        <div class="select_cat">
                            <label>
                                <input type="hidden"
                                       name="<?php echo esc_attr( $order_name ); ?>[target_cat][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
                                       value="0">
                                <input class="select_cat_checkbox" type="checkbox"
                                       name="<?php echo esc_attr( $order_name ); ?>[target_cat][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
									<?php checked( $check_slug, 1 ); ?>
                                       value="1">
								<?php echo esc_html( $tax_datum->name ); ?>
                            </label>
                        </div>
						<?php $per_page_data = APOP_UI::create_tax_per_page( get_option( '_apop_per_page' ), $order_tax, $tax_datum->term_id ); ?>
                        <div class="select-per-page">
                            <ul>
                                <li>
                                    <label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr( $order_tax ); ?>][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
                                               value="default"<?php checked( $per_page_data['_per_page'], 'default' ); ?>><?php _e( 'Follow display settings', APOP_DOMAIN ); ?>（<?php echo esc_html( get_option( 'posts_per_page' ) ); ?>
	                                    <?php _e( 'Posts', APOP_DOMAIN ); ?>）</label>
                                </li>
                                <li><label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr( $order_tax ); ?>][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
                                               value="all"<?php checked( $per_page_data['_per_page'], 'all' ); ?>><?php _e( 'Follow global settings', APOP_DOMAIN ); ?></label>
                                </li>
                                <li><label>
                                        <input class="per_page_cat" type="radio"
                                               name="_apop_per_page[<?php echo esc_attr( $order_tax ); ?>][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
                                               value="-1"<?php checked( $per_page_data['_per_page'], '-1' ); ?>><?php _e( 'All posts', APOP_DOMAIN ); ?></label>
                                </li>
                                <li class="set_number_list">
                                    <label><input class="per_page_cat set_number" type="radio"
                                                  name="_apop_per_page[<?php echo esc_attr( $order_tax ); ?>][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
                                                  value=""<?php echo esc_attr( $per_page_data['_checked'] ); ?>><?php _e( 'Set the number posts', APOP_DOMAIN ); ?>
                                        <input class="per_page_cat_input" type="text"
                                               name="_apop_per_page[<?php echo esc_attr( $order_tax ); ?>][<?php echo esc_attr( $tax_datum->term_id ); ?>]"
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