<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSFVP_Admin {

    const OPTION_GROUP = 'wsfvp_settings_group';
    const MENU_SLUG = 'wsfvp-settings';

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

        add_action(
            'admin_menu',
            [$this, 'move_menu_to_bottom'],
            999
        );
    }

    /**
     * Product fields
     */
    public function add_product_fields() {

        echo '<div class="options_group">';

        woocommerce_wp_checkbox([
            'id'          => '_wsfvp_enabled',
            'label'       => __('From Value Product', 'woosmooth-from-value-product'),
            'description' => __('Enable From Value Product mode.', 'woosmooth-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id'          => '_wsfvp_custom_link',
            'label'       => __('Custom Design Link', 'woosmooth-from-value-product'),
            'type'        => 'url',
            'placeholder' => 'https://example.com/',
            'description' => __('Leave empty to use the global settings', 'woosmooth-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id'          => '_wsfvp_custom_button_text',
            'label'       => __('Custom Button Text', 'woosmooth-from-value-product'),
            'type'        => 'text',
            'placeholder' => esc_attr__('My custom message', 'woosmooth-from-value-product'),
            'description' => __('Leave empty to use the global settings', 'woosmooth-from-value-product'),
        ]);

        woocommerce_wp_text_input([
            'id' => '_wsfvp_price_min',
            'label' => __('Minimum Price (From)', 'woosmooth-from-value-product'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        woocommerce_wp_text_input([
            'id' => '_wsfvp_price_max',
            'label' => __('Maximum Price (To)', 'woosmooth-from-value-product'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        woocommerce_wp_checkbox([
            'id' => '_wsfvp_hide_add_to_cart',
            'label' => __('Hide WooCommerce', 'woosmooth-from-value-product'),
            'description' => __('Hide WooCommerce button for this product only.', 'woosmooth-from-value-product'),
        ]);

        echo '</div>';
    }

    /**
     * Save product fields
     */
    public function save_product_fields($product_id) {

        update_post_meta(
            $product_id,
            '_wsfvp_enabled',
            isset($_POST['_wsfvp_enabled']) ? 'yes' : 'no'
        );

        if (isset($_POST['_wsfvp_custom_link'])) {

            update_post_meta(
                $product_id,
                '_wsfvp_custom_link',
                esc_url_raw($_POST['_wsfvp_custom_link'])
            );
        }

        if (isset($_POST['_wsfvp_custom_button_text'])) {

            update_post_meta(
                $product_id,
                '_wsfvp_custom_button_text',
                sanitize_text_field($_POST['_wsfvp_custom_button_text'])
            );
        }

        if (isset($_POST['_wsfvp_price_min'])) {
            update_post_meta(
                $product_id,
                '_wsfvp_price_min',
                floatval($_POST['_wsfvp_price_min'])
            );
        }

        if (isset($_POST['_wsfvp_price_max'])) {
            update_post_meta(
                $product_id,
                '_wsfvp_price_max',
                floatval($_POST['_wsfvp_price_max'])
            );
        }

        update_post_meta(
            $product_id,
            '_wsfvp_hide_add_to_cart',
            isset($_POST['_wsfvp_hide_add_to_cart']) ? 'yes' : 'no'
        );
    }

    /**
     * Settings page
     */
    public function add_settings_page() {

        add_submenu_page(
            'woocommerce',
             __('From Value Products With Custom Link', 'woosmooth-from-value-product'),
    __('From Value Products With Custom Link', 'woosmooth-from-value-product'),
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
            'wsfvp_default_link',
            [
                'sanitize_callback' => 'esc_url_raw',
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_open_in_new_tab',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_redirect_product_page',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_enabled_locations',
            [
                'sanitize_callback' => [$this, 'sanitize_locations'],
                'default' => [],
            ]
        );

        add_settings_section(
            'wsfvp_main_section',
            __('Global Settings', 'woosmooth-from-value-product'),
            '__return_false',
            self::MENU_SLUG
        );

        add_settings_field(
            'wsfvp_default_link',
            __('Default (Redirect) Link', 'woosmooth-from-value-product'),
            [$this, 'render_default_link_field'],
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        add_settings_field(
            'wsfvp_open_in_new_tab',
            __('Open Links In New Tab', 'woosmooth-from-value-product'),
            [$this, 'render_new_tab_field'],
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        add_settings_field(
            'wsfvp_redirect_product_page',
            __('Redirect Product Pages', 'woosmooth-from-value-product'),
            [$this, 'render_redirect_field'],
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        add_settings_field(
            'wsfvp_enabled_locations',
            __('Enable Functionality On', 'woosmooth-from-value-product'),
            [$this, 'render_locations_field'],
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_price_format',
            [
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'verbose', // verbose | compact
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_show_vat_label',
            [
                'sanitize_callback' => 'absint',
                'default' => 1,
            ]
        );

        add_settings_field(
            'wsfvp_price_format',
            __('Price Format', 'woosmooth-from-value-product'),
            function () {

                $value = get_option('wsfvp_price_format', 'verbose');

                ?>
                <select name="wsfvp_price_format">
                    <option value="verbose" <?php selected($value, 'verbose'); ?>>
                        <?php esc_html_e('From X to Y', 'woosmooth-from-value-product'); ?>
                    </option>
                    <option value="compact" <?php selected($value, 'compact'); ?>>
                        <?php esc_html_e('X - Y', 'woosmooth-from-value-product'); ?>
                    </option>
                </select>
                <?php
            },
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        add_settings_field(
            'wsfvp_show_vat_label',
            __('Show VAT Label', 'woosmooth-from-value-product'),
            function () {

                $value = get_option('wsfvp_show_vat_label', 1);

                ?>
                <input type="checkbox" name="wsfvp_show_vat_label" value="1" <?php checked($value, 1); ?>>
                <?php esc_html_e('Show VAT label on frontend', 'woosmooth-from-value-product'); ?>
                <?php
            },
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_hide_cart_shop',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        register_setting(
            self::OPTION_GROUP,
            'wsfvp_hide_cart_single',
            [
                'sanitize_callback' => 'absint',
                'default' => 0,
            ]
        );

        add_settings_field(
            'wsfvp_hide_cart_shop',
            __('Hide WooCommerce (Shop/Archives)', 'woosmooth-from-value-product'),
            function () {

                $value = get_option('wsfvp_hide_cart_shop', 0);

                ?>
                <input type="checkbox" name="wsfvp_hide_cart_shop" value="1" <?php checked($value, 1); ?>>
                <span><?php esc_html_e('Hide WooCommerce Purchase Button on product listings', 'woosmooth-from-value-product'); ?></span>
                <?php
            },
            self::MENU_SLUG,
            'wsfvp_main_section'
        );

        add_settings_field(
            'wsfvp_hide_cart_single',
            __('Hide WooCommerce (Single Product)', 'woosmooth-from-value-product'),
            function () {

                $value = get_option('wsfvp_hide_cart_single', 0);

                ?>
                <input type="checkbox" name="wsfvp_hide_cart_single" value="1" <?php checked($value, 1); ?>>
                <span><?php esc_html_e('Hide WooCommerce Purchase Button on product pages', 'woosmooth-from-value-product'); ?></span>
                <?php
            },
            self::MENU_SLUG,
            'wsfvp_main_section'
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

            <div class="wsfvp-logo">
                <img src="<?php echo esc_url(WSFVP_PLUGIN_URL . 'assets/img/logo/logo_slogan_full_color.png'); ?>" alt="WooSmooth Logo">
            </div>

            <h1 class="wsfvp-title"><?php esc_html_e('From Value Products With Custom Link', 'woosmooth-from-value-product'); ?></h1>

            <?php settings_errors(); ?>
            
            <div class="wsfvp-settings-wrap section-options">

                <form method="post" action="options.php">

                    <?php
                    settings_fields(self::OPTION_GROUP);
                    do_settings_sections(self::MENU_SLUG);
                    submit_button();
                    ?>

                </form>

            </div>

            <div class="wsfvp-settings-wrap section-translations">
                <h2><?php esc_html_e('Translations', 'woosmooth-from-value-product'); ?></h2>

                <p>
                    <?php esc_html_e(
                        'Default plugin texts can be translated using WPML, Loco Translate, or another WordPress translation plugin.',
                        'woosmooth-from-value-product'
                    ); ?>
                </p>

                <p>
                    <?php esc_html_e(
                        'Product-specific custom button text remains available on each product and can be translated through multilingual plugins such as WPML.',
                        'woosmooth-from-value-product'
                    ); ?>
                </p>

                <p>
                    <?php esc_html_e(
                        'Need help with translations? Contact WooSmooth for translation support.',
                        'woosmooth-from-value-product'
                    ); ?>
                </p>
            </div>

        </div>
        <?php
    }

    public function render_default_link_field() {

        $value = get_option('wsfvp_default_link', 'https://woosmooth.be/');

        ?>
        <input type="url"
               name="wsfvp_default_link"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <?php
        if ($value) {
            ?>&nbsp;<a href="<?php echo esc_attr($value); ?>" target="_blank" class="wsfvp_link"><?php esc_html_e('Test link/url', 'woosmooth-from-value-product'); ?></a><?php
        }
    }

    public function render_new_tab_field() {

        ?>
        <label>
            <input type="checkbox"
                   name="wsfvp_open_in_new_tab"
                   value="1"
                <?php checked(get_option('wsfvp_open_in_new_tab'), 1); ?>>

            <?php esc_html_e('Open links in a new tab', 'woosmooth-from-value-product'); ?>
        </label>
        <?php
    }

    public function render_redirect_field() {

        ?>
        <label>
            <input type="checkbox"
                   name="wsfvp_redirect_product_page"
                   value="1"
                <?php checked(get_option('wsfvp_redirect_product_page'), 1); ?>>

            <?php esc_html_e('Redirect single product pages', 'woosmooth-from-value-product'); ?>
        </label>
        <?php
    }

    public function render_locations_field() {

        $locations = get_option('wsfvp_enabled_locations', []);

        $options = [
            'shop'      => __('Shop page', 'woosmooth-from-value-product'),
            'archives'  => __('Category/tag archives', 'woosmooth-from-value-product'),
            'related'   => __('Related products', 'woosmooth-from-value-product'),
            'upsells'   => __('Upsells/cross-sells', 'woosmooth-from-value-product'),
            'single'    => __('Single product page', 'woosmooth-from-value-product'),
        ];

        foreach ($options as $value => $label) {

            ?>
            <label style="display:block;margin-bottom:8px;">

                <input type="checkbox"
                       name="wsfvp_enabled_locations[]"
                       value="<?php echo esc_attr($value); ?>"
                    <?php checked(in_array($value, $locations, true)); ?>>

                <?php echo esc_html($label); ?>

            </label>
            <?php
        }
    }

    public function move_menu_to_bottom() {

        global $submenu;

        if (!isset($submenu['woocommerce'])) {
            return;
        }

        foreach ($submenu['woocommerce'] as $key => $item) {

            if (
                isset($item[2]) &&
                $item[2] === self::MENU_SLUG
            ) {

                $menu_item = $item;

                unset($submenu['woocommerce'][$key]);

                $submenu['woocommerce'][] = $menu_item;

                break;
            }
        }
    }

}