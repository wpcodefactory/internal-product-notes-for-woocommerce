<?php
/**
 * Product Notes for WooCommerce - Main Class
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes' ) ) :

final class Alg_WC_Product_Notes {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_PRODUCT_NOTES_VERSION;

	/**
	 * @var   Alg_WC_Product_Notes The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Product_Notes Instance
	 *
	 * Ensures only one instance of Alg_WC_Product_Notes is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Product_Notes - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Product_Notes Constructor.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Pro
		if ( 'internal-product-notes-for-woocommerce-pro.php' === basename( ALG_WC_PRODUCT_NOTES_FILE ) ) {
			require_once( 'pro/class-alg-wc-pn-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * localize.
	 *
	 * @version 2.4.0
	 * @since   2.3.0
	 */
	function localize() {
		load_plugin_textdomain( 'product-notes-for-woocommerce', false, dirname( plugin_basename( ALG_WC_PRODUCT_NOTES_FILE ) ) . '/langs/' );
	}

	/**
	 * includes.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'class-alg-wc-pn-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_PRODUCT_NOTES_FILE ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version updated
		if ( get_option( 'alg_wc_product_notes_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * action_links.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_product_notes' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'internal-product-notes-for-woocommerce.php' === basename( ALG_WC_PRODUCT_NOTES_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/product-notes-for-woocommerce/">' .
				__( 'Go Pro', 'product-notes-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add_woocommerce_settings_tab.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-pn-settings.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function version_updated() {
		update_option( 'alg_wc_product_notes_version', $this->version );
	}

	/**
	 * plugin_url.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_PRODUCT_NOTES_FILE ) );
	}

	/**
	 * plugin_path.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_PRODUCT_NOTES_FILE ) );
	}

	/**
	 * get_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_id( $private_or_public ) {
		return ( 'private' === $private_or_public ? 'alg_wc_internal_product_note' : 'alg_wc_public_product_note' );
	}

}

endif;
