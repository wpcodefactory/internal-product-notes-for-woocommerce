<?php
/*
Plugin Name: Product Notes Tab & Private Admin Notes for WooCommerce
Plugin URI: https://wpfactory.com/item/product-notes-for-woocommerce/
Description: Add notes to WooCommerce products.
Version: 3.1.1
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: product-notes-for-woocommerce
Domain Path: /langs
WC tested up to: 9.8
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( 'internal-product-notes-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.9.2
	 * @since   2.4.0
	 */
	$plugin = 'internal-product-notes-for-woocommerce-pro/internal-product-notes-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		(
			is_multisite() &&
			array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
		)
	) {
		defined( 'ALG_WC_PRODUCT_NOTES_FILE_FREE' ) || define( 'ALG_WC_PRODUCT_NOTES_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_PRODUCT_NOTES_VERSION' ) || define( 'ALG_WC_PRODUCT_NOTES_VERSION', '3.1.1' );

defined( 'ALG_WC_PRODUCT_NOTES_FILE' ) || define( 'ALG_WC_PRODUCT_NOTES_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-pn.php';

if ( ! function_exists( 'alg_wc_pn' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Product_Notes to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_pn() {
		return Alg_WC_Product_Notes::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_pn' );
