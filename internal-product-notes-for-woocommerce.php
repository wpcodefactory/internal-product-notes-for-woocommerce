<?php
/*
Plugin Name: Product Notes for WooCommerce
Plugin URI: https://wpfactory.com/item/product-notes-for-woocommerce/
Description: Add notes to WooCommerce products.
Version: 2.8.1-dev
Author: Algoritmika Ltd
Author URI: https://algoritmika.com
Text Domain: product-notes-for-woocommerce
Domain Path: /langs
WC tested up to: 6.5
*/

defined( 'ABSPATH' ) || exit;

if ( 'internal-product-notes-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	$plugin = 'internal-product-notes-for-woocommerce-pro/internal-product-notes-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_PRODUCT_NOTES_VERSION' ) || define( 'ALG_WC_PRODUCT_NOTES_VERSION', '2.8.1-dev-20220718-1454' );

defined( 'ALG_WC_PRODUCT_NOTES_FILE' ) || define( 'ALG_WC_PRODUCT_NOTES_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-pn.php' );

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
