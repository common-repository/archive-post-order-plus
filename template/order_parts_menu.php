<?php if ( isset( $order_target, $order_target_type ) ): ?>
    <ul class="sort-menu-list"
        data-order_target="<?php echo esc_attr( $order_target ); ?>">
        <li><label>
                <input class="sort_menu" type="radio" name="<?php echo esc_attr( $order_target_type ); ?>"
                       value="1"<?php checked( $order_target, 1 ); ?>><?php _e('Standard + custom field sort', APOP_DOMAIN); ?></label></li>
        <li><label>
                <input class="sort_menu" type="radio" name="<?php echo esc_attr( $order_target_type ); ?>"
                       value="2"<?php checked( $order_target, 2 ); ?>><?php _e('Drag sort', APOP_DOMAIN); ?></label></li>
    </ul>
<?php endif;