<?php
/**
 * Product Notes for WooCommerce - Main Class
 *
 * @version 3.0.0
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
	 * core.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $core;

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
	 * @version 3.0.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Load libs
		if ( is_admin() ) {
			require_once plugin_dir_path( ALG_WC_PRODUCT_NOTES_FILE ) . 'vendor/autoload.php';
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'internal-product-notes-for-woocommerce-pro.php' === basename( ALG_WC_PRODUCT_NOTES_FILE ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-pn-pro.php';
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
		load_plugin_textdomain(
			'product-notes-for-woocommerce',
			false,
			dirname( plugin_basename( ALG_WC_PRODUCT_NOTES_FILE ) ) . '/langs/'
		);
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 2.9.2
	 * @since   2.9.2
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = ( defined( 'ALG_WC_PRODUCT_NOTES_FILE_FREE' ) ?
				array( ALG_WC_PRODUCT_NOTES_FILE, ALG_WC_PRODUCT_NOTES_FILE_FREE ) :
				array( ALG_WC_PRODUCT_NOTES_FILE )
			);
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
			}
		}
	}

	/**
	 * includes.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-core.php';
	}

	/**
	 * admin.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function admin() {

		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_PRODUCT_NOTES_FILE ), array( $this, 'action_links' ) );

		// "Recommendations" page
		$this->add_cross_selling_library();

		// WC Settings tab as WPFactory submenu item
		$this->move_wc_settings_tab_to_wpfactory_menu();

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
	 * @version 3.0.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_product_notes' ) . '">' .
			__( 'Settings', 'product-notes-for-woocommerce' ) .
		'</a>';
		if ( 'internal-product-notes-for-woocommerce.php' === basename( ALG_WC_PRODUCT_NOTES_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/product-notes-for-woocommerce/">' .
				__( 'Go Pro', 'product-notes-for-woocommerce' ) .
			'</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PRODUCT_NOTES_FILE ) );
		$cross_selling->init();

	}

	/**
	 * move_wc_settings_tab_to_wpfactory_menu.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {

		if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
			return;
		}

		$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_product_notes',
			'menu_title'         => __( 'Product Notes', 'product-notes-for-woocommerce' ),
			'page_title'         => __( 'Product Notes', 'product-notes-for-woocommerce' ),
		) );

	}

	/**
	 * add_woocommerce_settings_tab.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-pn-settings.php';
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
		return ( 'private' === $private_or_public ?
			'alg_wc_internal_product_note' :
			'alg_wc_public_product_note'
		);
	}

}

endif;
