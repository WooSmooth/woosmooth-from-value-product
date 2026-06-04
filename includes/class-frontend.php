<?php

if (!defined('ABSPATH')) {
    exit;
}

class WSFVP_Frontend {

    public function __construct() {

        add_filter(
            'woocommerce_loop_add_to_cart_link',
            [$this, 'replace_loop_button'],
            10,
            3
        );

        add_filter(
            'woocommerce_product_single_add_to_cart_text',
            [$this, 'replace_single_button_text']
        );

        add_action(
            'woocommerce_single_product_summary',
            [$this, 'replace_single_product_button'],
            1
        );

        add_filter(
            'woocommerce_get_price_html',
            [$this, 'custom_price_html'],
            10,
            2
        );

        add_action(
            'template_redirect',
            [$this, 'redirect_product_page']
        );
        
        add_filter(
            'woocommerce_is_purchasable',
            [$this, 'maybe_disable_purchasable'], 
            10,
            2
        );
    }

    /**
     * Check if product is enabled
     */
    private function is_from_value_product($product_id) {

        return get_post_meta(
            $product_id,
            '_wsfvp_enabled',
            true
        ) === 'yes';
    }

    /**
     * Get enabled locations
     */
    private function get_enabled_locations() {

        $locations = get_option(
            'wsfvp_enabled_locations',
            []
        );

        if (!is_array($locations)) {
            return [];
        }

        return $locations;
    }

    /**
     * Check if functionality enabled for location
     */
    private function is_location_enabled($location) {

        return in_array(
            $location,
            $this->get_enabled_locations(),
            true
        );
    }

    /**
     * Get design URL
     */
    private function get_design_url($product_id) {

        $custom = get_post_meta(
            $product_id,
            '_wsfvp_custom_link',
            true
        );

        if (!empty($custom)) {
            return esc_url($custom);
        }

        return esc_url(
            get_option('wsfvp_default_link', '#')
        );
    }

    /**
     * Get button text
     */
    private function get_button_text($product_id) {

        $custom_text = get_post_meta(
            $product_id,
            '_wsfvp_custom_button_text',
            true
        );

        if (!empty($custom_text)) {
            return esc_html($custom_text);
        }

        $labels = get_option('wsfvp_button_label_translations', []);

        if (is_array($labels)) {
            $locale = $this->get_current_locale();

            if (!empty($labels[$locale])) {
                return esc_html($labels[$locale]);
            }

            $language_code = substr($locale, 0, 2);

            foreach ($labels as $stored_locale => $label) {
                if (
                    strpos($stored_locale, $language_code . '_') === 0 &&
                    !empty($label)
                ) {
                    return esc_html($label);
                }
            }
        }

        return esc_html__('Go to url', 'woosmooth-from-value-product');
    }

    /**
     * Link target
     */
    private function get_link_target() {

        $new_tab = get_option(
            'wsfvp_open_in_new_tab',
            0
        );

        return $new_tab
            ? ' target="_blank" rel="noopener noreferrer" '
            : '';
    }

    /**
     * Replace archive/shop buttons
     */
    public function replace_loop_button($html, $product, $args) {

        if (!$this->is_from_value_product($product->get_id())) {
            return $html;
        }

         if ($this->should_hide_add_to_cart($product->get_id())) {
            return '';
        }

        /**
         * Detect WooCommerce loop context
         */
        $location = 'shop';

        if (is_product_category() || is_product_tag()) {
            $location = 'archives';
        }

        /**
         * Related products
         */
        global $woocommerce_loop;

        if (
            isset($woocommerce_loop['name']) &&
            $woocommerce_loop['name'] === 'related'
        ) {
            $location = 'related';
        }

        /**
         * Upsells/cross-sells
         */
        if (
            isset($woocommerce_loop['name']) &&
            in_array(
                $woocommerce_loop['name'],
                ['up-sells', 'cross-sells'],
                true
            )
        ) {
            $location = 'upsells';
        }

        if (!$this->is_location_enabled($location)) {
            return $html;
        }

        $url = $this->get_design_url($product->get_id());

        return sprintf(
            '<a href="%s" class="button"%s>%s</a>',
            esc_url($url),
            $this->get_link_target(),
            $this->get_button_text($product->get_id())
        );
    }

    /**
     * Replace single product button text
     */
    public function replace_single_button_text($text) {

        global $product;

        if (!$product) {
            return $text;
        }

        if (!$this->is_location_enabled('single')) {
            return $text;
        }

        if (!$this->is_from_value_product($product->get_id())) {
            return $text;
        }

        if ($this->should_hide_add_to_cart($product->get_id())) {
            return '';
        }

        return $this->get_button_text($product->get_id());
    }

    /**
     * Replace single product add to cart button
     */
    public function replace_single_product_button() {

        if (!$this->is_location_enabled('single')) {
            return;
        }

        global $product;

        if (!$product) {
            return;
        }

        if (!$this->is_from_value_product($product->get_id())) {
            return;
        }

        if ($this->should_hide_add_to_cart($product->get_id())) {
            return; // 🔥 THIS is the real fix
        }

        // Remove default button safely
        remove_action(
            'woocommerce_single_product_summary',
            'woocommerce_template_single_add_to_cart',
            30
        );

        add_action(
            'woocommerce_single_product_summary',
            function () use ($product) {

                $url = $this->get_design_url($product->get_id());

                echo sprintf(
                    '<a href="%s" class="single_add_to_cart_button wc_single_link_button button alt"%s>%s</a>',
                    esc_url($url),
                    $this->get_link_target(),
                    $this->get_button_text($product->get_id())
                );
            },
            30
        );
    }

    /**
     * Custom price display
     */
    public function custom_price_html($price, $product) {

        if (!$this->is_from_value_product($product->get_id())) {
            return $price;
        }

        $min = get_post_meta($product->get_id(), '_wsfvp_price_min', true);
        $max = get_post_meta($product->get_id(), '_wsfvp_price_max', true);

        if (empty($min) && empty($max)) {
            return $price;
        }

        $min_price = wc_price($min);
        $max_price = wc_price($max);

        $format = get_option('wsfvp_price_format', 'verbose');

        $vat_label = '';

        if (get_option('wsfvp_show_vat_label', 1)) {
            $vat_label = ' ' . esc_html__(
                'Incl. VAT',
                'woosmooth-from-value-product'
            );
        }

        if ($format === 'compact') {
            return $min_price . ' - ' . $max_price . $vat_label;
        }

        return sprintf(
            '%s %s %s %s',
            esc_html__('From', 'woosmooth-from-value-product'),
            $min_price,
            esc_html__('to', 'woosmooth-from-value-product'),
            $max_price
        ) . $vat_label;
    }

    /**
     * Redirect single product pages
     */
    public function redirect_product_page() {

        /**
         * Never redirect admin
         */
        if (is_admin()) {
            return;
        }

        /**
         * Prevent AJAX issues
         */
        if (wp_doing_ajax()) {
            return;
        }

        /**
         * Only product pages
         */
        if (!is_product()) {
            return;
        }

        /**
         * Redirect disabled
         */
        if (!get_option('wsfvp_redirect_product_page', 0)) {
            return;
        }

        global $post;

        if (!$post) {
            return;
        }

        /**
         * Product not enabled
         */
        if (!$this->is_from_value_product($post->ID)) {
            return;
        }

        $url = $this->get_design_url($post->ID);

        /**
         * Prevent empty redirects
         */
        if (empty($url)) {
            return;
        }

        /**
         * Allow external redirects
         */
        wp_redirect($url);

        exit;
    }    

    private function should_hide_add_to_cart($product_id) {

        // Product-level override
        $product_hide = get_post_meta($product_id, '_wsfvp_hide_add_to_cart', true) === 'yes';

        // Global settings
        $global_shop = get_option('wsfvp_hide_cart_shop', 0);
        $global_single = get_option('wsfvp_hide_cart_single', 0);

        if ($product_hide) {
            return true;
        }

        if (is_product() && $global_single) {
            return true;
        }

        if (!is_product() && $global_shop) {
            return true;
        }

        return false;
    }

    public function maybe_disable_purchasable($purchasable, $product) {

        if (!$product) {
            return $purchasable;
        }

        if (!$this->is_from_value_product($product->get_id())) {
            return $purchasable;
        }

        if ($this->should_hide_add_to_cart($product->get_id())) {
            return false;
        }

        return $purchasable;
    }

    private function get_current_locale() {

        if (defined('ICL_LANGUAGE_CODE')) {
            $current_language = apply_filters('wpml_current_language', null);

            if (!empty($current_language)) {
                $active_languages = apply_filters(
                    'wpml_active_languages',
                    null,
                    ['skip_missing' => 0]
                );

                if (
                    is_array($active_languages) &&
                    isset($active_languages[$current_language]['default_locale'])
                ) {
                    return $active_languages[$current_language]['default_locale'];
                }
            }
        }

        return determine_locale();
    }
}