=== WooSmooth From Value Product With Custom Link ===
Contributors: woosmooth, collisioncourse
Tags: woocommerce, product, custom link, redirect, configurator, catalog mode, quote request
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Requires Plugins: woocommerce
Stable tag: 2.1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert WooCommerce products into configurable "From Value Products" with custom links, price ranges, external configurators, and optional catalog mode functionality.

== Description ==

WooSmooth From Value Product With Custom Link transforms standard WooCommerce products into configurable "From Value Products". Instead of using the default WooCommerce add-to-cart flow, products can redirect customers to external configurators, product builders, design tools, quote forms, or custom landing pages.

Requires WooCommerce.

The plugin automatically checks for WooCommerce and will not run unless WooCommerce is installed and active.

= Key Features =

* Convert WooCommerce products into configurable "From Value Products"
* Redirect products to external configurators, builders, or landing pages
* Product-specific custom URLs
* Product-specific custom button text
* Configurable price range display
* Multiple price display formats
* Optional product page redirects
* Optional Add to Cart hiding
* Location-based frontend control
* Translation ready
* WPML, Polylang and Loco Translate compatible

= Product-Level Controls =

Each product can be individually configured.

Available options:

* Enable or disable "From Value Product" mode
* Set a custom external URL
* Set a custom button text
* Configure minimum and maximum price values
* Hide WooCommerce purchase buttons for specific products
* Override global plugin settings

= Global Settings =

A dedicated settings page is available under:

WooCommerce → From Value Products

Available settings include:

* Default redirect URL
* Open links in the same tab or a new tab
* Redirect single product pages
* Enable functionality on specific frontend locations:
  * Shop page
  * Category and tag archives
  * Related products
  * Upsells and cross-sells
  * Single product pages
* Price display format:
  * Verbose: "From €100 to €200"
  * Compact: "€100 - €200"
* VAT label visibility (show/hide)
* Global Add to Cart visibility controls

= Frontend Behaviour =

When enabled, the plugin modifies WooCommerce frontend behaviour:

* Replaces Add to Cart buttons with custom links
* Replaces shop loop buttons
* Replaces single product purchase buttons
* Optionally hides WooCommerce purchase buttons
* Supports custom price ranges
* Supports configurable price display formats
* Supports optional product page redirects

= Price Range Display =

Verbose format:

From €1.000,00 to €2.000,00 Incl. VAT

Compact format:

€1.000,00 - €2.000,00 Incl. VAT

VAT labels can be shown or hidden globally and are fully translatable.

= Translation Ready =

WooSmooth From Value Product With Custom Link is fully translation-ready.

Included language packs:

* English (default)
* Dutch (Belgium) – nl_BE
* French (Belgium) – fr_BE
* French (France) – fr_FR
* German (Germany) – de_DE

Compatible with:

* WPML
* Polylang
* Loco Translate
* WordPress language packs

Text domain:

woosmooth-from-value-product

== Installation ==

1. Upload the plugin folder to:

   `/wp-content/plugins/woosmooth-from-value-product/`

2. Activate the plugin via:

   Plugins → Installed Plugins

3. Configure the plugin:

   WooCommerce → From Value Products

4. Edit a WooCommerce product and enable:

   Product Data → From Value Product

5. Optionally configure custom links, button text, price ranges, and frontend behaviour.

== Frequently Asked Questions ==

= Does this replace WooCommerce checkout? =

No. The plugin replaces the product purchasing flow when enabled, but does not modify WooCommerce checkout itself.

= Can I still use normal WooCommerce products? =

Yes. Only products with "From Value Product" enabled are affected.

= Can I use different links per product? =

Yes. Every product can have its own custom URL and button text.

= What happens if no custom link is configured? =

The global default redirect URL is used automatically.

= Can links open in a new tab? =

Yes. This can be configured globally.

= Does it support variable products? =

Yes. However, price range functionality is primarily intended for simple products and configurable product setups.

= Is WooSmooth From Value Product With Custom Link translation-ready? =

Yes. The plugin includes bundled language files and supports WPML, Polylang, and Loco Translate.

== Changelog ==

= 2.1.3 =

* Added WooSmooth icons to Update package.

= 2.1.2 =

* Added WooSmooth icons.

= 2.1.1 =

* Added native WooCommerce plugin dependency support.

= 2.1.0 =

* Added multilingual support for link button labels.
* Supported locales:
  * English (default)
  * Dutch (Belgium) – nl_BE
  * French (Belgium) – fr_BE
  * French (France) – fr_FR
  * German (Germany) – de_DE

= 2.0.0 =

* Rebranded as WooSmooth From Value Product With Custom Link
* Improved translation support
* Added bundled language packs (NL-BE, FR-BE, FR-FR, DE-DE)
* Moved settings page to the WooCommerce menu
* Improved plugin structure and code consistency
* Updated text domain and localization framework

= 1.1.1 =

* Improved readme documentation
* General cleanup and documentation improvements

= 1.1.0 =

* Added product price range support
* Added multiple price display formats
* Added VAT label visibility controls
* Added Add to Cart visibility controls
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

= 2.1.3 =

Added WooSmooth icons to Update package.

= 2.1.2 =

Added WooSmooth icons.

= 2.1.1 =

Added native WooCommerce plugin dependency support.

= 2.1.0 =

Added multilingual support for link button labels.

= 2.0.0 =

WooSmooth From Value Product With Custom Link introduces a complete WooSmooth rebrand, improved translation support, bundled language packs, WooCommerce admin integration, and internal code improvements. Updating is recommended for all users.

= 1.1.1 =

Documentation and compatibility improvements.

= 1.1.0 =

Introduces price range support, improved frontend controls, VAT label options, and enhanced Add to Cart visibility management.

= 1.0.2 =

Minor improvements and styling updates.

= 1.0.1 =

Compatibility and cleanup improvements.

= 1.0.0 =

Initial stable release.
