jQuery(function ($) {

    //tab menu
    change_tab_menu();

    //custom orders
    order_list();

    //order type
    change_sort_box();

    //Tax select
    select_tax();

    //per page
    change_search_per_page();
    change_tax_per_page();

    change_normal_field_sort();

    custom_field_select();

    function change_tab_menu() {
        const apop_submit_type = $('#apop_submit_type');
        const order_nav_list = $('.post-order-nav li');
        const order_box = $('.post-order-box');

        init();
        click_menu();

        function init() {
            const submit_type = apop_submit_type.val();
            order_nav_list.removeClass('en');
            order_box.hide();
            $('.post-order-nav li:eq(' + submit_type + ')').addClass('en');
            $('.post-order-box:eq(' + submit_type + ')').show()
        }

        function click_menu() {
            order_nav_list.on('click', function () {
                if (!$(this).hasClass('en')) {
                    const target_index = $(this).index();
                    order_nav_list.removeClass('en');
                    $(this).addClass('en');
                    order_box.hide();
                    $('.post-order-box:eq(' + target_index + ')').show();
                    apop_submit_type.val(target_index);
                }
            });
        }
    }

    function order_list() {
        const cat_ul_count = $('.post-order-list').length; //カテゴリ総数
        for (let i = 0; i < cat_ul_count; i++) {
            const my_list = $('.post-order-list:eq(' + i + ')');
            my_list.sortable(
                {
                    update: function () {
                        const list_count = $('.product-list', my_list).length;
                        for (let j = 0; j < list_count; j++) {
                            let list_order_num = j + 1;
                            $('.list-order:eq(' + j + ')', my_list).val(list_order_num);
                            $('.list-order:eq(' + j + ')', my_list).siblings('.sort-num-label').text(list_order_num);
                        }
                    }
                }
            );
            my_list.disableSelection();
        }
    }

    function change_sort_box() {
        const s_radio = $('.sort_menu');
        const sort_menu_size = $('.sort-menu-list').length;

        $('.sort_box').hide();
        for (let i = 0; i < sort_menu_size; i++) {
            const elm = $('.sort-menu-list:eq(' + i + ')');
            const type_index = elm.data('order_target') - 1;
            elm.siblings('.sort_box:eq(' + type_index + ')').show();
        }

        s_radio.on('click', function () {
            const target_index = $(this).val() - 1;
            $(this).parents('.list-orders-inner').find('.sort_box').hide();
            $(this).parents('.list-orders-inner').find('.sort_box:eq(' + target_index + ')').show();
        })
    }

    function select_tax() {
        const select_cat_checkbox = $('.select_cat_checkbox');
        const input_chk_size = select_cat_checkbox.length;
        for (let i = 0; i < input_chk_size; i++) {
            const chk = $('.select_cat_checkbox:eq(' + i + '):checked').length;
            const elm = $('.select_cat:eq(' + i + ')');
            if (chk === 1) {
                elm.siblings('.select-per-page').show();
            } else {
                elm.siblings('.select-per-page').hide();
            }
        }

        select_cat_checkbox.on('click', function () {
            const checked = $(this).prop('checked');
            if (checked) {
                $(this).parent().parent().siblings('.select-per-page').show(200);
            } else {
                $(this).parent().parent().siblings('.select-per-page').hide(200);
            }
        });

    }

    function change_search_per_page() {
        const per_page_cat = $('.per_page_search');
        const per_page_input = $('.per_page_search_input');

        if (per_page_input.val() === '') {
            per_page_input.prop('disabled', true);
        } else {
            per_page_input.prop('disabled', false);
        }

        per_page_cat.on('click', function () {
            if ($(this).val() !== 'default' && $(this).val() !== '-1' && $(this).val() !== 'all') {
                per_page_input.prop('disabled', false);
            } else {
                per_page_input.val('');
                per_page_input.prop('disabled', true);
            }
        })
    }

    function change_tax_per_page() {
        const set_number = $('.set_number');
        const input_chk_size = set_number.length;
        for (let i = 0; i < input_chk_size; i++) {
            const input_num_box = $('.set_number:eq(' + i + ')').siblings('.per_page_cat_input');
            if (input_num_box.val() === '') {
                input_num_box.prop('disabled', true);
            } else {
                input_num_box.prop('disabled', false);
            }
        }

        $('.per_page_cat').on('click', function () {
            if ($(this).hasClass('set_number')) {
                $(this).siblings('.per_page_cat_input').prop('disabled', false);
            } else {
                $(this).parents('li').find('.per_page_cat_input').val('').prop('disabled', true);
            }
        });
    }

    function change_normal_field_sort() {
        const s_box = $('.sort_box');
        const targets = '.sort_date_check, .sort_title_check, .sort_ID_check, .sort_modified_check';
        s_box.find(targets).each(function () {
            if ($(this).prop('checked') === false) {
                $(this).parents('.product-list-sort-type').find('.order_param').prop('disabled', true);
                const disable_list = $(this).parents('.sort_box').find('.disable-normal-list');
                $(this).parents('li').appendTo(disable_list);
            }
        });
        $(targets).on('click', function () {
            if ($(this).prop('checked') === true) {
                $(this).parents('.product-list-sort-type').find('.order_param').prop('disabled', false);
                const enable_list = $(this).parents('.sort_box').find('.post-order-list');
                $(this).parents('li').appendTo(enable_list).removeClass('no-order').hide().fadeIn(200);
            } else {
                $(this).parents('.product-list-sort-type').find('.order_param').prop('disabled', true);
                const disable_list = $(this).parents('.sort_box').find('.disable-normal-list');
                $(this).parents('li').appendTo(disable_list).addClass('no-order').hide().fadeIn(200);
            }
        });
    }

    function custom_field_select() {
        const s_box = $('.sort_box');
        const targets = $('.custom-field-type');
        const en_dis_check = $('.custom_field_check');

        // カスタムフィールドソートの有効・無効を判定する。
        // 有効の場合はカスタムフィールド選択or追加のステータスに応じてテキストエリアとセレクトタグenableに変更し表示を切り替える。
        // 無効の場合はカスタムフィールドのテキストエリアとセレクトタグをDisableにし無効ボックスに移動する。
        s_box.find('.custom_field_check').each(function () {
            if ($(this).prop('checked') === false) {
                const disable_list = $(this).parents('.sort_box').find('.disable-normal-list');
                $(this).parents('li').appendTo(disable_list);
                dis_list($(this));
            } else {
                const target_type = $(this).parents('.product-list-sort-type').find('.custom-field-type').filter(':checked').val();
                en_dis_list($(this), target_type);
            }
        });

        //有効・無効チェックボックスクリック時
        en_dis_check.on('click', function () {
            if ($(this).prop('checked') === true) {
                const enable_list = $(this).parents('.sort_box').find('.post-order-list');
                const target_type = $(this).parents('.product-list-sort-type').find('.custom-field-type').filter(':checked').val();
                $(this).parents('.product-list-sort-type').find('.order_param').prop('disabled', false);
                $(this).parents('li').appendTo(enable_list).removeClass('no-order').hide().fadeIn(200);
                en_dis_list($(this), target_type);
            } else {
                const disable_list = $(this).parents('.sort_box').find('.disable-normal-list');
                $(this).parents('.product-list-sort-type').find('.order_param').prop('disabled', true);
                $(this).parents('li').appendTo(disable_list).addClass('no-order').hide().fadeIn(200);
                dis_list($(this));
            }
        });

        //選択・追加のカスタムフィールド種類ラジオボタンのクリック
        targets.on('click', function () {
            en_dis_list($(this), $(this).val());
        });

        function dis_list(obj) {
            obj.parents('.product-list-sort-type').find('.custom_field_key').prop('disabled', true);
            obj.parents('.product-list-sort-type').find('.custom_field_key_select').prop('disabled', true);
        }

        function en_dis_list(obj, type) {
            let select_msg = 'Select from existing custom fields';
            let add_msg = 'Added custom field for APOP';
            if ($('.post-setting-box').hasClass('apop-lang-ja')) {
                select_msg = '既存のカスタムフィールドから選択';
                add_msg = 'APOP専用カスタムフィールドを追加';
            }
            if (type === 1) {
                obj.parents('.product-list-sort-type').find('.custom_field_key_select').prop('disabled', false).show();
                obj.parents('.product-list-sort-type').find('.custom_field_key').prop('disabled', true).hide();
                obj.parents('.product-list-sort-type').find('.custom-field-select-alert').text(select_msg);
            } else if (type === 2) {
                obj.parents('.product-list-sort-type').find('.custom_field_key').prop('disabled', false).show();
                obj.parents('.product-list-sort-type').find('.custom_field_key_select').prop('disabled', true).hide();
                obj.parents('.product-list-sort-type').find('.custom-field-select-alert').text(add_msg);
            }
        }
    }

});