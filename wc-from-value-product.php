<?php
/**
 * Plugin Name: WooCommerce From Value Product With Custom Link
 * Plugin URI: https://github.com/WooSmooth/wc-from-value-product
 * Description: Adds "From Value Product" functionality to WooCommerce products.
 * Version: 1.1.2
 * Author: WooSmooth | CollisionCourse
 * Author URI: https://www.collisioncourse.be
 * Text Domain: wc-from-value-product
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 10.0
 */

/**
 * Plugin updater
 * @YahnisElsts
 */
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/WooSmooth/wc-from-value-product',
	__FILE__,
	'wc-from-value-product'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

/**
 * Core
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WCFVP_VERSION')) {
    define('WCFVP_VERSION', '1.1.1');
}

if (!class_exists('WC_From_Value_Product')) {

    class WC_From_Value_Product {

        public function __construct() {

            if (!defined('WCFVP_PLUGIN_PATH')) {
                define('WCFVP_PLUGIN_PATH', plugin_dir_path(__FILE__));
            }

            if (!defined('WCFVP_PLUGIN_URL')) {
                define('WCFVP_PLUGIN_URL', plugin_dir_url(__FILE__));
            }

            add_action('plugins_loaded', [$this, 'init']);
            add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
            add_action('wp_enqueue_scripts', [$this, 'app_assets']);
        }

        /**
         * Initialize plugin
         */
        public function init() {

            if (!$this->is_woocommerce_active()) {
                add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
                return;
            }

            load_plugin_textdomain(
                'wc-from-value-product',
                false,
                dirname(plugin_basename(__FILE__)) . '/languages'
            );

            require_once WCFVP_PLUGIN_PATH . 'includes/class-admin.php';
            require_once WCFVP_PLUGIN_PATH . 'includes/class-frontend.php';

            new WCFVP_Admin();
            new WCFVP_Frontend();
        }

        /**
         * Check WooCommerce dependency
         */
        private function is_woocommerce_active() {
            return class_exists('WooCommerce');
        }

        /**
         * Admin notice when WooCommerce is missing
         */
        public function woocommerce_missing_notice() {

            if (!current_user_can('activate_plugins')) {
                return;
            }

            echo '<div class="notice notice-error"><p>';

            echo esc_html__(
                'WooCommerce From Value Product requires WooCommerce to be installed and active.',
                'wc-from-value-product'
            );

            echo '</p></div>';
        }

        /**
         * Load admin assets
         */
        public function admin_assets($hook) {

            if (
                $hook !== 'product' &&
                strpos($hook, 'wcfvp-settings') === false
            ) {
                return;
            }

            wp_enqueue_style(
                'wcfvp-admin',
                WCFVP_PLUGIN_URL . 'assets/css/admin.css',
                [],
                WCFVP_VERSION
            );
        }

        /**
         * Load app assets
         */
        public function app_assets() {

            wp_enqueue_style(
                'wcfvp-app',
                WCFVP_PLUGIN_URL . 'assets/css/app.css',
                [],
                WCFVP_VERSION
            );
        }
    }

    new WC_From_Value_Product();
}

/**
 * Prevent activation without WooCommerce
 */
register_activation_hook(__FILE__, function () {

    if (!class_exists('WooCommerce')) {

        deactivate_plugins(plugin_basename(__FILE__));

        wp_die(
            esc_html__('WooCommerce From Value Product requires WooCommerce to be installed and active.', 'wc-from-value-product'),
            esc_html__('Plugin dependency check', 'wc-from-value-product'),
            ['back_link' => true]
        );
    }
});