<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCFVP_Admin {

    const OPTION_GROUP = 'wcfvp_settings_group';
    const MENU_SLUG = 'wcfvp-settings';

    public function __construct() {

        add_action(
            'woocommerce_product_options_general_product_data',
            [$this, 'add_product_fields']
        );

        add_action(
            'woocommerce_process_product_meta',
            [$this, 'save_product_fields']
        );

        add_action(
            'admin_menu',
            [$this, 'add_settings_page']
        );

        add_action(
            'admin_init',
            [$this, 'register_settings']
        );
    }

    /**
     * Product fields
     */
    public function add_product_fields() {

        echo '<div class="options_group">';

        woocommerce_wp_checkbox([
            'id'          => '_wcfvp_enabled',
            'label'       => __('From Value Product', 'wc-from-value-product'),
            'description' => __('Enable From Value Product mode.', 'wc-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id'          => '_wcfvp_custom_link',
            'label'       => __('Custom Design Link', 'wc-from-value-product'),
            'type'        => 'url',
            'placeholder' => 'https://example.com/',
            'description' => __('Leave empty to use the global settings', 'wc-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id'          => '_wcfvp_custom_button_text',
            'label'       => __('Custom Button Text', 'wc-from-value-product'),
            'type'        => 'text',
            'placeholder' => __('My custom message', 'wc-from-value-product'),
            'description' => __('Leave empty to use the global settings', 'wc-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id' => '_wcfvp_price_min',
            'label' => __('Minimum Price (From)', 'wc-from-value-product'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        woocommerce_wp_text_input([
            'id' => '_wcfvp_price_max',
            'label' => __('Maximum Price (To)', 'wc-from-value-product'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        woocommerce_wp_checkbox([
            'id' => '_wcfvp_hide_add_to_cart',
            'label' => __('Hide WooCommerce', 'wc-from-value-product'),
            'description' => __('Hide WooCommerce button for this product only.', 'wc-from-value-product'),
        ]);

        echo '</div>';
    }

    /**
     * Save product fields
     */
    public function save_product_fields($product_id) {

        update_post_meta(
            $product_id,
            '_wcfvp_enabled',
            isset($_POST['_wcfvp_enabled']) ? 'yes' : 'no'
        );

        if (isset($_POST['_wcfvp_custom_link'])) {

            update_post_meta(
                $product_id,
                '_wcfvp_custom_link',
                esc_url_raw($_POST['_wcfvp_custom_link'])
            );
        }

        if (isset($_POST['_wcfvp_custom_button_text'])) {

            update_post_meta(
                $product_id,
                '_wcfvp_custom_button_text',
                sanitize_text_field($_POST['_wcfvp_custom_button_text'])
            );
        }

        if (isset($_POST['_wcfvp_price_min'])) {
            update_post_meta(
                $product_id,
                '_wcfvp_price_min',
                floatval($_POST['_wcfvp_price_min'])
            );
        }

        if (isset($_POST['_wcfvp_price_max'])) {
            update_post_meta(
                $product_id,
                '_wcfvp_price_max',
                floatval($_POST['_wcfvp_price_max'])
            );
        }

        update_post_meta(
            $product_id,
            '_wcfvp_hide_add_to_cart',
            isset($_POST['_wcfvp_hide_add_to_cart']) ? 'yes' : 'no'
        );
    }

    /**
     * Settings page
     */
    public function add_settings_page() {

        add_submenu_page(
            'woocommerce',
            __('From Value Products', 'wc-from-value-product'),
            __('From Value Products', 'wc-from-value-product'),
            'manage_woocommerce',
            self::MENU_SLUG,
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_default_link',
            [
                'sanitize_callback' => 'esc_url_raw',
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_default_button_text',
            [
                'sanitize_callback' => 'sanitize_text_field',
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_open_in_new_tab',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_redirect_product_page',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_enabled_locations',
            [
                'sanitize_callback' => [$this, 'sanitize_locations'],
                'default' => [],
            ]
        );

        add_settings_section(
            'wcfvp_main_section',
            __('Global Settings', 'wc-from-value-product'),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'wcfvp_default_link',
            __('Default Design Link', 'wc-from-value-product'),
            [$this, 'render_default_link_field'],
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_default_button_text',
            __('Default Button Text', 'wc-from-value-product'),
            [$this, 'render_default_button_text_field'],
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_open_in_new_tab',
            __('Open Links In New Tab', 'wc-from-value-product'),
            [$this, 'render_new_tab_field'],
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_redirect_product_page',
            __('Redirect Product Pages', 'wc-from-value-product'),
            [$this, 'render_redirect_field'],
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_enabled_locations',
            __('Enable Functionality On', 'wc-from-value-product'),
            [$this, 'render_locations_field'],
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_price_format',
            [
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'verbose', // verbose | compact
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_show_vat_label',
            [
                'sanitize_callback' => 'absint',
                'default' => 1,
            ]
        );

        add_settings_field(
            'wcfvp_price_format',
            __('Price Format', 'wc-from-value-product'),
            function () {

                $value = get_option('wcfvp_price_format', 'verbose');

                ?>
                <select name="wcfvp_price_format">
                    <option value="verbose" <?php selected($value, 'verbose'); ?>>
                        <?php esc_html_e('From X to Y', 'wc-from-value-product'); ?>
                    </option>
                    <option value="compact" <?php selected($value, 'compact'); ?>>
                        <?php esc_html_e('X - Y', 'wc-from-value-product'); ?>
                    </option>
                </select>
                <?php
            },
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_show_vat_label',
            __('Show VAT Label', 'wc-from-value-product'),
            function () {

                $value = get_option('wcfvp_show_vat_label', 1);

                ?>
                <input type="checkbox" name="wcfvp_show_vat_label" value="1" <?php checked($value, 1); ?>>
                <?php esc_html_e('Show VAT label on frontend', 'wc-from-value-product'); ?>
                <?php
            },
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_hide_cart_shop',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wcfvp_hide_cart_single',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        add_settings_field(
            'wcfvp_hide_cart_shop',
            __('Hide WooCommerce (Shop/Archives)', 'wc-from-value-product'),
            function () {

                $value = get_option('wcfvp_hide_cart_shop', 0);

                ?>
                <input type="checkbox" name="wcfvp_hide_cart_shop" value="1" <?php checked($value, 1); ?>>
                <span><?php esc_html_e('Hide WooCommerce button on product listings', 'wc-from-value-product'); ?></span>
                <?php
            },
            self::MENU_SLUG,
            'wcfvp_main_section'
        );

        add_settings_field(
            'wcfvp_hide_cart_single',
            __('Hide WooCommerce (Single Product)', 'wc-from-value-product'),
            function () {

                $value = get_option('wcfvp_hide_cart_single', 0);

                ?>
                <input type="checkbox" name="wcfvp_hide_cart_single" value="1" <?php checked($value, 1); ?>>
                <span><?php esc_html_e('Hide WooCommerce button on product pages', 'wc-from-value-product'); ?></span>
                <?php
            },
            self::MENU_SLUG,
            'wcfvp_main_section'
        );
    }

    /**
     * Sanitize locations
     */
    public function sanitize_locations($input) {

        if (!is_array($input)) {
            return [];
        }

        return array_map('sanitize_text_field', $input);
    }

    /**
     * Settings page
     */
    public function render_settings_page() {
    ?>
    <div class="wrap">

        <div class="wcfvp-logo">
            <img src="<?php echo esc_url(WCFVP_PLUGIN_URL . 'assets/img/logo/logo_slogan_full_color.png'); ?>" alt="WooSmooth Logo">
        </div>

        <h1 class="wcfvp-title"><?php esc_html_e('From Value Products', 'wc-from-value-product'); ?></h1>

        <div class="wcfvp-settings-wrap">

            <form method="post" action="options.php">

                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::MENU_SLUG);
                submit_button();
                ?>

            </form>

        </div>

    </div>
    <?php
}

    public function render_default_link_field() {

        $value = get_option('wcfvp_default_link', '');

        ?>
        <input type="url"
               name="wcfvp_default_link"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <?php
    }

    public function render_default_button_text_field() {

        $value = get_option(
            'wcfvp_default_button_text',
            __('Start designing', 'wc-from-value-product')
        );

        ?>
        <input type="text"
               name="wcfvp_default_button_text"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <?php
    }

    public function render_new_tab_field() {

        ?>
        <label>
            <input type="checkbox"
                   name="wcfvp_open_in_new_tab"
                   value="1"
                <?php checked(get_option('wcfvp_open_in_new_tab'), 1); ?>>

            <?php esc_html_e('Open links in a new tab', 'wc-from-value-product'); ?>
        </label>
        <?php
    }

    public function render_redirect_field() {

        ?>
        <label>
            <input type="checkbox"
                   name="wcfvp_redirect_product_page"
                   value="1"
                <?php checked(get_option('wcfvp_redirect_product_page'), 1); ?>>

            <?php esc_html_e('Redirect single product pages', 'wc-from-value-product'); ?>
        </label>
        <?php
    }

    public function render_locations_field() {

        $locations = get_option('wcfvp_enabled_locations', []);

        $options = [
            'shop'      => __('Shop page', 'wc-from-value-product'),
            'archives'  => __('Category/tag archives', 'wc-from-value-product'),
            'related'   => __('Related products', 'wc-from-value-product'),
            'upsells'   => __('Upsells/cross-sells', 'wc-from-value-product'),
            'single'    => __('Single product page', 'wc-from-value-product'),
        ];

        foreach ($options as $value => $label) {

            ?>
            <label style="display:block;margin-bottom:8px;">

                <input type="checkbox"
                       name="wcfvp_enabled_locations[]"
                       value="<?php echo esc_attr($value); ?>"
                    <?php checked(in_array($value, $locations, true)); ?>>

                <?php echo esc_html($label); ?>

            </label>
            <?php
        }
    }
}