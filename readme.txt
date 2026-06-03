=== WooCommerce From Value Product ===
Contributors: woosmooth, collisioncourse
Tags: woocommerce, product, from, value, custom link, redirect, catalog mode
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds "From Value Product" functionality to WooCommerce products, allowing products to redirect to external design tools or custom URLs instead of the default add-to-cart flow.

== Description ==

WooCommerce From Value Product transforms standard WooCommerce products into configurable "From Value Products". Instead of using the default add-to-cart flow, products can redirect users to external configurators, product builders, or custom landing pages.

This plugin is ideal for:
* Product configurators
* Custom design tools
* Quote-based products
* External checkout flows
* Lead generation products
* Catalog mode setups

= Product-level controls =

Each product can be individually configured.

Features include:
* Enable or disable "From Value Product" mode per product
* Set custom external URL per product
* Set custom button text per product
* Set minimum and maximum price values (price range support)
* Override global settings per product

= Global settings =

A dedicated settings page is available under:

WooCommerce → From Value Products

Available global options:
* Default design URL fallback
* Default button text fallback
* Open links in same tab or new tab
* Redirect single product pages to external URL
* Enable functionality per location:
  * Shop page
  * Category and tag archives
  * Related products
  * Upsells and cross-sells
  * Single product pages
* Price display format selection:
  * Verbose: “From €100 to €200”
  * Compact: “€100 - €200”
* VAT label display control (show/hide)
* Custom VAT label translation support (via Loco Translate or WordPress translations)

= Frontend behavior =

The plugin modifies WooCommerce frontend output when enabled:

* Replaces add-to-cart buttons with custom links
* Replaces shop loop product buttons
* Replaces single product purchase button
* Optionally hides Add to Cart button globally or per product
* Supports full price range display (min → max)
* Displays "Starts from" pricing logic
* Handles WooCommerce sale pricing formatting
* Optional redirect of single product pages

= Price display examples =

Price formats:

Verbose format:
From €1.000,00 to €2.000,00 Incl. VAT

Compact format:
€1.000,00 - €2.000,00 Incl. VAT

VAT label can be:
* Shown or hidden globally
* Translated via WordPress translation tools (Loco Translate, WPML, Polylang)

= Translation ready =

The plugin is fully translation-ready and supports:
* WPML
* Polylang
* Loco Translate

Text domain:
wc-from-value-product

== Installation ==

1. Upload the plugin folder to:
`/wp-content/plugins/wc-from-value-product/`

2. Activate the plugin via:
WordPress Admin → Plugins → Installed Plugins

3. Configure global settings:
WordPress Admin → Settings → From Value Products

4. Edit WooCommerce products and enable:
Product Data → From Value Product

5. (Optional) Configure price range fields and custom settings per product.

== Screenshots ==

1. Product edit screen showing "From Value Product" settings
2. Global settings page

== Frequently Asked Questions ==

= Does this replace WooCommerce checkout? =
No. It replaces the product purchasing flow when enabled, but does not modify checkout itself.

= Can I still use normal WooCommerce products? =
Yes. Only products with "From Value Product" enabled are affected.

= Can I use different links per product? =
Yes. Each product can have its own custom URL and button text.

= What happens if no custom link is set? =
The global default URL is used automatically.

= Can I open links in a new tab? =
Yes. This is configurable in global settings.

= Does it support variable products? =
Yes, but pricing range is best used with simple products or controlled variations.

== Changelog ==

= 1.1.2 =
* Move menu to WooCommerce

= 1.1.1 =
* Improved readme format

= 1.1.0 =
* Added product price range support (min / max)
* Added global price display format (verbose / compact)
* Added VAT label visibility toggle
* Added full Add to Cart hiding (shop + single + per product)
* Improved frontend rendering logic
* Improved WooCommerce compatibility

= 1.0.2 =
* Improved CSS handling and admin styling

= 1.0.1 =
* Removed unused code blocks
* Improved WordPress.org compatibility
* Improved admin styling

= 1.0.0 =
* Initial release
* Product-level custom links
* Global settings
* Custom button texts
* Redirect support
* Price customization
* Frontend location controls

== Upgrade Notice ==

= 1.1.2 =
Move menu to WooCommerce

= 1.1.1 =
This update improved the readme format

= 1.1.0 =
This update introduces major improvements including price range support, improved frontend control, and enhanced Add to Cart visibility management. Update is recommended for all users.

= 1.0.2 =
Minor improvements and styling updates.

= 1.0.1 =
Compatibility and cleanup improvements.

= 1.0.0 =
Initial stable release.