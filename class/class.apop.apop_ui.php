<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('APOP_UI')) {

    require_once __DIR__ . '/../util/apop-customfield-select.php';
    require_once __DIR__ . '/../util/apop-customposts.php';

    class APOP_UI {

        use CUSTOMFIELD_SELECT, CUSTOMPOSTS;

        public static function get_order_type($type) {
            $type_data = get_option($type);
            if (!$type_data) {
                return 1;
            }

            return $type_data;
        }

        public static function get_all_search_normal_posts($key) {
            $meta_key = '_apop_post_' . $key;
            $args = self::create_search_normal_args($meta_key, $key);
            $posts_data = get_posts($args);
            foreach ($posts_data as $i => $post_data) {
                $order = $i + 1;
                $no_order = self::is_sort_post_registered($post_data->ID, $meta_key) ? '' : ' no-order';
                echo '<li class="product-list' . esc_attr($no_order) . '">'
                    . '<span class="sort-num-label">' . esc_html($order) . '</span>' . esc_html($post_data->post_title)
                    . '<input type="hidden" class="list-order" 
                     name="_apop_post_' . esc_attr($key) . '[post_sort][' . esc_attr($post_data->ID) . ']" 
                     value="' . esc_attr($order) . '">
                     </li>';
            }
        }

        private static function create_search_normal_args($meta_key, $key = 'normal'): array {
            $post_type = array('post');
            if ($key == 'search') {
                $add_post_type = array_values(get_post_types(array(
                            'public' => true,
                            '_builtin' => false,
                        )
                    )
                );
                $post_type = array_merge($post_type, $add_post_type);
            }

            return array(
                'post_type' => $post_type,
                'post_status' => array('publish', 'draft'),
                'posts_per_page' => -1,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => $meta_key,
                        'compare' => 'EXISTS',
                    ),
                    array(
                        'key' => $meta_key,
                        'compare' => 'NOT EXISTS',
                    ),
                ),
            );
        }

        private static function get_all_custom_posts(): array {
            $custom_posts = array_values(get_post_types(array('public' => true, '_builtin' => false)));
            $custom_post_data = array();
            foreach ($custom_posts as $custom_post) {
                $label = get_post_type_object($custom_post)->label;
                $custom_post_data[$custom_post] = $label;
            }

            return $custom_post_data;
        }

        private static function get_all_taxonomies($key) {
            if ($key !== 'taxonomy') {
                return get_terms(array('taxonomy' => $key, 'get' => 'all'));
            }
            $all_custom_tax = get_taxonomies(array('public' => true, '_builtin' => false));
            $custom_tax_list = array();
            foreach ($all_custom_tax as $custom_tax) {
                $custom_tax_list = array_merge($custom_tax_list, get_terms(array(
                    'taxonomy' => $custom_tax,
                    'get' => 'all'
                )));
            }

            return $custom_tax_list;
        }

        public static function get_cat_tag_list($target, $key): array {
            $opt = get_option('_apop_' . $target . '_order');

            if (!isset($opt['target_cat'])) {
                return array();
            }

            if ($key == 'taxonomy') {
                return self::create_custom_tax_term($opt);
            } else {
                return self::create_tax_term($opt, $key);
            }
        }

        private static function create_custom_tax_term($opt): array {
            $tax_data = array();
            foreach ($opt['target_cat'] as $tax_id => $status) {
                //フラグが立っているカスタムタクソノミーは情報を取得する
                if ($status) {
                    $args = array(
                        'taxonomy' => get_term($tax_id)->taxonomy,
                        'hide_empty' => 0,
                        'include' => $tax_id,
                    );
                    $tax_data = array_merge($tax_data, get_terms($args));

                }
            }
            if (count($tax_data) > 0) {
                return $tax_data;
            }

            return array();
        }

        private static function create_tax_term($opt, $key) {
            $include = array();
            foreach ($opt['target_cat'] as $tax_id => $status) {
                //フラグが立っているタクソノミーIDを取得
                if ($status) {
                    $include[] = $tax_id;
                }
            }
            $include_tax = implode(',', $include);
            if (!empty($include_tax)) {
                $args = array(
                    'taxonomy' => $key,
                    'hide_empty' => 0,
                    'include' => $include_tax,
                );

                return get_terms($args);
            }

            return array();
        }

        public static function create_order_list($tax_data, $tax_key): string {
            $return_data = self::get_sort_post_list($tax_data->term_id, $tax_key, $tax_data->taxonomy);
            $list = array();
            foreach ($return_data['data'] as $key => $target_post) {
                $sort_num = $key + 1;
                $no_order = self::is_sort_post_registered($target_post->ID, $return_data['meta_key']) ? '' : ' no-order';
                $list[] = '
<li class="product-list' . esc_html($no_order) . '">' . '<span class="sort-num-label">' . esc_html($sort_num) . '</span>' . get_the_title($target_post->ID) . '
<input type="hidden" class="list-order" 
name="_apop_post_' . esc_html(self::create_post_sort_key($tax_data->taxonomy, $tax_key)) . '[post_sort][' . esc_html($tax_data->term_id) . '][' . esc_html($target_post->ID) . ']" 
value="' . esc_html($sort_num) . '">
</li>';
            }

            return implode(PHP_EOL, $list);
        }

        private static function is_sort_post_registered($id, $key): bool {
            if (get_post_meta($id, $key, true)) {
                return true;
            }

            return false;
        }

        private static function get_sort_post_list($tax_id, $search_param, $tax_name): array {
            $post_types = array('post');
            $custom_post_types = array_values(get_post_types(array('public' => true, '_builtin' => false)));
            $args = array(
                'post_type' => array_merge($post_types, $custom_post_types),
                'post_status' => array('publish', 'draft'),
                'posts_per_page' => -1,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
            );
            $meta_key = '_apop_post_' . self::create_post_sort_key($tax_name, $search_param) . '_' . $tax_id;
            self::create_sort_post_list_meta_query($args, $meta_key);
            self::create_post_tax_query($args, $search_param, $tax_name, $tax_id);

            return array(
                'meta_key' => $meta_key,
                'data' => get_posts($args)
            );
        }

        private static function create_sort_post_list_meta_query(&$args, $meta_key) {

            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => $meta_key,
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => $meta_key,
                    'compare' => 'NOT EXISTS',
                ),
            );
        }

        public static function create_post_sort_key($tax_name, $tax_key) {
            if ($tax_key == 'taxonomy') {
                return 'tax';
            }

            return $tax_name;
        }

        private static function create_post_tax_query(&$args, $key, $tax_name, $tax_id) {
            if ($key == 'taxonomy') {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $tax_name,
                        'field' => 'term_id',
                        'terms' => $tax_id,
                        'include_children' => false
                    )
                );
            } else {
                $args[$key] = $tax_id;
            }
        }

        public static function create_cat_per_page($opt_per_page, $type): array {
            $cat_per_page = $opt_per_page[$type] ?? 'default';
            $checked = '';
            $cat_per_page_num = '';
            if (isset($opt_per_page[$type])) {
                if ($opt_per_page[$type] != 'default'
                    && $opt_per_page[$type] != '-1'
                    && $opt_per_page[$type] != 'all') {
                    $checked = ' checked="checked"';
                    $cat_per_page_num = $cat_per_page;
                }
            }

            return array(
                '_per_page' => $cat_per_page,
                '_checked' => $checked,
                '_per_page_num' => $cat_per_page_num,
            );
        }

        public static function disp_tax_setting($key, $title, $order_name) {
            $tax_data = APOP_UI::get_all_taxonomies($key);
            if (count($tax_data) > 0) {
                $order_tax = $key == 'post_tag' ? 'tag' : $key;
                echo '<tr><th scope="row">' . esc_html($title) . '</th><td>';
                if (isset($tax_data, $order_name, $order_tax)) {
                    include APOP_PLUGIN_PATH . 'template/setting_parts_taxonomy.php';
                }
                echo '</td></tr>';
            }
        }

        public static function disp_customposts_setting($key, $title, $order_name) {
            $custom_post_data = APOP_UI::get_all_custom_posts();
            if (count($custom_post_data) > 0) {
                echo '<tr><th scope="row">' . esc_html($title) . '</th><td>';
                if (isset($custom_post_data, $order_name, $key)) {
                    include APOP_PLUGIN_PATH . 'template/setting_parts_customposts.php';
                }
                echo '</td></tr>';
            }
        }

        public static function create_custom_posts_per_page($opt_per_page, $type, $slug): array {
            $cat_per_page = $opt_per_page[$type][$slug] ?? 'default';
            $checked = '';
            $cat_per_page_num = '';
            if (isset($opt_per_page[$type][$slug])) {
                if ($opt_per_page[$type][$slug] != 'default'
                    && $opt_per_page[$type][$slug] != '-1'
                    && $opt_per_page[$type][$slug] != 'all') {
                    $checked = ' checked="checked"';
                    $cat_per_page_num = $cat_per_page;
                }
            }

            return array(
                '_per_page' => $cat_per_page,
                '_checked' => $checked,
                '_per_page_num' => $cat_per_page_num,
            );
        }

        public static function create_tax_per_page($opt_per_page, $type, $id): array {
            $cat_per_page = $opt_per_page[$type][$id] ?? 'default';
            $checked = '';
            $cat_per_page_num = '';
            if (isset($opt_per_page[$type][$id])) {
                if ($opt_per_page[$type][$id] != 'default'
                    && $opt_per_page[$type][$id] != '-1'
                    && $opt_per_page[$type][$id] != 'all') {
                    $checked = ' checked="checked"';
                    $cat_per_page_num = $cat_per_page;
                }
            }

            return array(
                '_per_page' => $cat_per_page,
                '_checked' => $checked,
                '_per_page_num' => $cat_per_page_num,
            );
        }

        public static function create_search_normal_list($type, $id = null) {
            $name_keys = self::create_name_keys($id, $type);
            $name_key = $name_keys['name_key'];
            $get_option_key = $name_keys['get_option_key'];
            $order_param_base = get_option($get_option_key);
            $order_param = '';
            if (is_null($id)) {
                $order_param = $order_param_base;
            }
            if (isset($order_param_base[$id])) {
                $order_param = $order_param_base[$id];
            }
            self::create_normal_sort_list($name_key, $order_param);
        }

        private static function create_custom_field_sort_type($name_key, $target_key, $cnv_order_params) {
            $meta_key = $cnv_order_params[$target_key]['meta_key'];
            $value_type = $cnv_order_params[$target_key]['value_type'];
            $custom_field_type = $cnv_order_params[$target_key]['custom_field_type'];
            $name_meta_key = '_' . $name_key . '[' . $target_key . '][field][meta_key]';
            $name_value_type = '_' . $name_key . '[' . $target_key . '][field][value_type]';
            $name_custom_field_type = '_' . $name_key . '[' . $target_key . '][field][custom_field_type]';
            $custom_field_val_1 = $custom_field_type == '1' ? $meta_key : '';
            $custom_field_val_2 = $custom_field_type == '2' ? $meta_key : '';
            echo '<div class="sort-custom-field">
					<div class="sort-custom-field-types">
					<div class="sort-custom-field-inner-label">
					<label>
					<input type="radio" 
					class="custom-field-type"
					name="' . esc_attr($name_custom_field_type) . '"
					value="1"' . esc_attr(self::set_search_normal_checked($custom_field_type, '1')) . '>' . __('Select', APOP_DOMAIN) . '</label>
					<label>
					<input type="radio" 
					class="custom-field-type"
					name="' . esc_attr($name_custom_field_type) . '"
					value="2"' . esc_attr(self::set_search_normal_checked($custom_field_type, '2')) . '>' . __('Add', APOP_DOMAIN) . '</label>
					</div>';
            echo '<input type="text" class="select-custom-field-input custom_field_key_select" name="' . esc_attr($name_meta_key) . '" value="' . esc_attr($custom_field_val_1) . '" required>
					<input type="text" class="custom_field_key" name="' . esc_attr($name_meta_key) . '" value="' . esc_attr($custom_field_val_2) . '" required>
					</div>
					<div class="custom-field-select-alert"></div>
					<div class="sort-custom-field-inner">
					<div class="sort-custom-field-inner-label">' . __('Type', APOP_DOMAIN) . '：</div>
					<label class="sort-custom-field-text-label">
					<input type="radio" 
					class="custom-field-meta-value" 
					name="' . esc_attr($name_value_type) . '" 
					value="meta_value"' . esc_attr(self::set_search_normal_checked($value_type, 'meta_value')) . '>' . __('Text', APOP_DOMAIN) . '</label>
					<label><input type="radio" 
					class="custom-field-meta-value" 
					name="' . esc_attr($name_value_type) . '" 
					value="meta_value_num"' . esc_attr(self::set_search_normal_checked($value_type, 'meta_value_num')) . '>' . __('Number', APOP_DOMAIN) . '</label>
					</div>
				</div>';
        }

        private static function set_order_list_param($order_param, $target_key): array {

            $param = array(
                'use' => 0,
                'sort' => 2,
                'no_order_class' => ' no-order',
            );

            if (strpos($target_key, 'custom_field') !== false) {
                $param[$target_key] = array(
                    'meta_key' => '',
                    'value_type' => 'meta_value',
                    'custom_field_type' => '1',
                );
            }

            if (isset($order_param[$target_key])) {
                $param = array(
                    'use' => $order_param[$target_key]['use'] ?? 0,
                    'sort' => $order_param[$target_key]['sort'] ?? 2,
                    'no_order_class' => !$order_param[$target_key]['use'] ? ' no-order' : '',
                );
                if (strpos($target_key, 'custom_field') !== false) {
                    $param[$target_key] = array(
                        'meta_key' => $order_param[$target_key]['field']['meta_key'] ?? '',
                        'value_type' => $order_param[$target_key]['field']['value_type'] ?? 'meta_value',
                        'custom_field_type' => $order_param[$target_key]['field']['custom_field_type'] ?? '1',
                    );
                }
            }

            return $param;

        }

        private static function create_name_keys($id, $type): array {
            if (is_null($id)) {
                return array(
                    'name_key' => 'apop_' . $type . '_order_param',
                    'get_option_key' => '_' . 'apop_' . $type . '_order_param',
                );
            } else {
                return array(
                    'name_key' => 'apop_tax_order_param[' . $id . ']',
                    'get_option_key' => '_apop_tax_order_param',
                );
            }
        }

        private static function set_search_normal_target_keys($post_apop_search_order_param): array {

            $set_keys = array(
                'date',
                'title',
                'ID',
                'modified',
                'custom_field',
                'custom_field_2',
                'custom_field_3',
                'custom_field_4',
            );

            if ($post_apop_search_order_param) {
                $register_keys = array_keys($post_apop_search_order_param);

                return array_unique(array_merge($register_keys, $set_keys));
            }

            return $set_keys;
        }

        private static function set_search_normal_checked($param, $default): string {
            if ($param == $default) {
                return ' checked="checked"';
            }
            return '';
        }

        public static function input_post_filter($var_name, $type) {
            if ($type == 'array') {
                return filter_input(INPUT_POST, $var_name, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
            }
            if ($type == 'str') {
                return filter_input(INPUT_POST, $var_name, FILTER_SANITIZE_STRING);
            }
            return '';
        }

        public static function create_normal_sort_list($name_key, $order_param) {
            $target_keys = self::set_search_normal_target_keys($order_param);
            $target_values = array(
                'date' => __('Published', APOP_DOMAIN),
                'title' => __('Post Title', APOP_DOMAIN),
                'ID' => 'ID',
                'modified' => __('Modified', APOP_DOMAIN),
                'custom_field' => __('Custom filed 1', APOP_DOMAIN),
                'custom_field_2' => __('Custom filed 2', APOP_DOMAIN),
                'custom_field_3' => __('Custom filed 3', APOP_DOMAIN),
                'custom_field_4' => __('Custom filed 4', APOP_DOMAIN),
            );
            foreach ($target_keys as $target_key) {
                $cnv_order_params = self::set_order_list_param($order_param, $target_key);
                $use = $cnv_order_params['use'];
                $sort = $cnv_order_params['sort'];
                $no_order_class = $cnv_order_params['no_order_class'];
                $name_use_key = '_' . $name_key . '[' . $target_key . '][use]';
                $name_sort_key = '_' . $name_key . '[' . $target_key . '][sort]';
                if (strpos($target_key, 'custom_field') !== false) {
                    $target_key_check_class = 'custom_field_check';
                } else {
                    $target_key_check_class = 'sort_' . $target_key . '_check';
                }

                echo '<li class="product-list' . esc_attr($no_order_class) . '">
						<div class="product-list-type-label"><b>' . esc_attr($target_values[$target_key]) . '</b></div>
                        <div class="product-list-sort-type">
                        <label>
                            <input type="hidden" name="' . esc_attr($name_use_key) . '" 
                            value="0"' . esc_attr(self::set_search_normal_checked($use, 0)) . '>
                            <span class="en_dis_label">' . __('Enabled', APOP_DOMAIN) . '</span>：<input class="' . esc_html($target_key_check_class) . '" 
                            type="checkbox" name="' . esc_attr($name_use_key) . '" value="1"' . esc_attr(self::set_search_normal_checked($use, 1)) . '>
                        </label>
                        <label>
                            <input class="order_param" type="radio"
                                      name="' . esc_attr($name_sort_key) . '"
                                      value="1"' . esc_attr(self::set_search_normal_checked($sort, 1)) . '>' . __('Asc', APOP_DOMAIN) . '</label>
                        <label>
                            <input class="order_param" type="radio"
                                      name="' . esc_attr($name_sort_key) . '"
                                      value="2"' . esc_attr(self::set_search_normal_checked($sort, 2)) . '>' . __('Desc', APOP_DOMAIN) . '</label>';

                if (strpos($target_key, 'custom_field') !== false) {
                    self::create_custom_field_sort_type($name_key, $target_key, $cnv_order_params);
                }

                echo '</div>
                </li>';
            }
        }

    }
}