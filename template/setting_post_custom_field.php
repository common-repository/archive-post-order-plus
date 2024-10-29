<div class="order_setting_custom_field_box">
	<?php if ( isset( $this->order_field ) && count( $this->order_field ) > 0 ): ?>
        <dl class="apop-setting-list-dd">
			<?php foreach ( $this->order_field as $type => $items ): ?>
                <dt><?php echo esc_html( $this->labels[ $type ] ); ?></dt>
                <dd>
                    <ul>
						<?php foreach ( $items as $idx => $item ): ?>
							<?php if ( ! empty( $item ) ): ?>
								<?php
								if ( $this->custom_field_type[ $type ][ $idx ] == '2' ) {
									$custom_field_prefix = APOP_CUSTOM_FIELD_PREFIX;
									$disabled            = '';
									$read_only_class     = '';
								} else {
									$custom_field_prefix = '';
									$disabled            = ' disabled';
									$read_only_class     = __( '* Change from custom field', APOP_DOMAIN );
								}
								?>
                                <li>
                                    <label><?php echo esc_html( $item ); ?></label>
                                    <div class="sort-custom-field-input">
                                        <input type="text"
                                               name="<?php echo esc_attr( $custom_field_prefix . $item ); ?>"
                                               value="<?php echo esc_attr( $this->get_custom_field_data( $item, $custom_field_prefix ) ); ?>"<?php echo esc_attr( $disabled ); ?>/>
										<?php echo esc_html( $read_only_class ); ?>
                                    </div>
                                </li>
							<?php endif; ?>
						<?php endforeach; ?>
                    </ul>
                </dd>
			<?php endforeach; ?>
        </dl>
	<?php endif; ?>
</div>