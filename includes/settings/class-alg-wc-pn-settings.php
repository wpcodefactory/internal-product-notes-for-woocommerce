<?php
/**
 * Product Notes for WooCommerce - Settings
 *
 * @version 2.6.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Settings' ) ) :

class Alg_WC_Product_Notes_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_product_notes';
		$this->label = __( 'Product Notes', 'product-notes-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_custom' ), PHP_INT_MAX, 3 );
		// Sections
		require_once( 'class-alg-wc-pn-settings-section.php' );
		$sections = array(
			'private' => new Alg_WC_Product_Notes_Settings_Section( '',             __( 'Private notes', 'product-notes-for-woocommerce' ), 'private' ),
			'public'  => new Alg_WC_Product_Notes_Settings_Section( 'public_notes', __( 'Public notes', 'product-notes-for-woocommerce' ),  'public' ),
		);
		$sections['advanced'] = require_once( 'class-alg-wc-pn-settings-advanced.php' );
	}

	/**
	 * sanitize_custom.
	 *
	 * @version 2.3.1
	 * @since   2.2.0
	 */
	function sanitize_custom( $value, $option, $raw_value ) {
		if ( ! empty( $option['alg_wc_pn_sanitize'] ) ) {
			switch ( $option['alg_wc_pn_sanitize'] ) {
				case 'textarea':
					$value = wp_kses_post( trim( $raw_value ) );
					break;
				default:
					$func  = $option['alg_wc_pn_sanitize'];
					$value = ( function_exists( $func ) ? $func( $raw_value ) : $value );
					break;
			}
		}
		return $value;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'product-notes-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'product-notes-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'product-notes-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'product-notes-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ), PHP_INT_MAX );
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'product-notes-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * save.
	 *
	 * @version 1.1.2
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		do_action( 'alg_wc_product_note_settings_after_save' );
	}

}

endif;

return new Alg_WC_Product_Notes_Settings();
