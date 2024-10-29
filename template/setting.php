<div class="post-setting-box">
    <form action="" method="post">
		<?php wp_nonce_field( 'sh_options' ); ?>
        <h2><?php _e( 'Settings', APOP_DOMAIN ); ?></h2>
        <table class="form-table apop-form-table">
            <tr>
                <th scope="row"><?php _e( 'Global settings', APOP_DOMAIN ); ?></th>
                <td>
                    <dl class="apop-setting-list">
                        <dt><?php _e( 'Displayed per page', APOP_DOMAIN ); ?></dt>
                        <dd><?php $per_page_data = APOP_UI::create_cat_per_page( get_option( '_apop_per_page' ), 'search' ); ?>
                            <ul>
                                <li>
                                    <label>
                                        <input class="per_page_search" type="radio" name="_apop_per_page[search]"
                                               value="default"<?php checked( $per_page_data['_per_page'], 'default' ); ?>><?php _e( 'Follow display settings', APOP_DOMAIN ); ?>
                                        （<?php echo esc_html( get_option( 'posts_per_page' ) ); ?>
										<?php _e( 'Posts', APOP_DOMAIN ); ?>）</label>
                                </li>
                                <li><label>
                                        <input class="per_page_search" type="radio" name="_apop_per_page[search]"
                                               value="-1"<?php checked( $per_page_data['_per_page'], '-1' ); ?>><?php _e( 'All posts', APOP_DOMAIN ); ?>
                                    </label>
                                </li>
                                <li>
                                    <label><input class="per_page_search" type="radio"
                                                  name="_apop_per_page[search]"
                                                  value="" <?php echo esc_attr( $per_page_data['_checked'] ); ?>><?php _e( 'Set the number posts', APOP_DOMAIN ); ?>
                                        <input class="per_page_search_input" type="text"
                                               name="_apop_per_page[search]"
                                               value="<?php echo esc_attr( $per_page_data['_per_page_num'] ); ?>"
                                               required>
                                    </label>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                </td>
            </tr>
			<?php APOP_UI::disp_tax_setting( 'category', __( 'Categories setting', APOP_DOMAIN ), '_apop_cat_order' ); ?>
			<?php APOP_UI::disp_tax_setting( 'post_tag', __( 'Tags setting', APOP_DOMAIN ), '_apop_tag_order' ); ?>
			<?php APOP_UI::disp_tax_setting( 'taxonomy', __( 'Custom Taxonomies setting', APOP_DOMAIN ), '_apop_tax_order' ); ?>
	        <?php APOP_UI::disp_customposts_setting( 'custompost_archive', __( 'Custom Posts Archive setting', APOP_DOMAIN ), '_apop_custompost_archive_order' ); ?>
        </table>
        <p class="submit"><input type="submit" name="Submit" class="button-primary"
                                 value="<?php _e( 'Save changes', APOP_DOMAIN ); ?>"/>
    </form>
</div>