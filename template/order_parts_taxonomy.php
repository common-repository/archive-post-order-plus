<?php if ( isset( $tax_lists, $tax_title_text ) ): ?>
	<?php foreach ( $tax_lists as $tax_key => $tax_list ) : ?>
        <div class="list-orders-outer">
			<?php if ( count( $tax_list ) > 0 ): ?>
				<?php foreach ( $tax_list as $tax_data ): ?>
                    <div class="list-orders-inner">
                        <h3><?php echo esc_html( $tax_data->name ); ?></h3>
						<?php
						$order_target_data = get_option( '_apop_tax_sort_type' );
						if ( isset( $order_target_data[ $tax_data->term_id ] ) ) {
							$order_target = $order_target_data[ $tax_data->term_id ];
						} else {
							$order_target = 2;
						}
						?>
                        <ul class="sort-menu-list"
                            data-order_target="<?php echo esc_attr( $order_target ); ?>">
                            <li>
                                <label>
                                    <input class="sort_menu" type="radio"
                                           name="_apop_tax_sort_type[<?php echo esc_attr( $tax_data->term_id ); ?>]"
                                           value="2"<?php checked( $order_target, 2 ); ?>><?php _e( 'Standard + custom field sort', APOP_DOMAIN ); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input class="sort_menu" type="radio"
                                           name="_apop_tax_sort_type[<?php echo esc_attr( $tax_data->term_id ); ?>]"
                                           value="1"<?php checked( $order_target, 1 ); ?>><?php _e( 'Drag sort', APOP_DOMAIN ); ?>
                                </label>
                            </li>
                        </ul>

                        <div class="sort_box">
                            <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                            <ul class="post-order-list drag-sort">
								<?php echo APOP_UI::create_order_list( $tax_data, $tax_key ); ?>
                            </ul>
                        </div>

                        <div class="sort_box">
                            <p><?php _e( 'Click "Save Changes" to register the sort.', APOP_DOMAIN ); ?></p>
                            <div class="enable-box">
                                <h4><?php _e( 'Enabled', APOP_DOMAIN ); ?></h4>
                                <ul class="post-order-list search-normal-sort">
									<?php APOP_UI::create_search_normal_list( 'tax', $tax_data->term_id ); ?>
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
                <p class="no-registered-exp"><?php _e( 'No', APOP_DOMAIN ); ?> <?php echo esc_html( $tax_title_text . ' ' ); ?><?php _e( 'has been selected.', APOP_DOMAIN ); ?></p>
			<?php endif; ?>
        </div>
	<?php endforeach; ?>
<?php endif;