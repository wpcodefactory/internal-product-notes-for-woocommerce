<?php
/**
 * Product Notes for WooCommerce - Settings
 *
 * @version 3.2.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Settings' ) ) :

	/**
	 * Alg_WC_PN_Settings class.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 */
	class Alg_WC_PN_Settings extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 3.2.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'alg_wc_product_notes';
			$this->label = __( 'Product Notes', 'product-notes-for-woocommerce' );

			parent::__construct();

			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_custom' ), PHP_INT_MAX, 3 );

			// Sections.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-settings-section.php';
			$sections = array(
				'private' => new Alg_WC_PN_Settings_Section(
					'',
					__( 'Private notes', 'product-notes-for-woocommerce' ),
					'private'
				),
				'public'  => new Alg_WC_PN_Settings_Section(
					'public_notes',
					__( 'Public notes', 'product-notes-for-woocommerce' ),
					'public'
				),
			);

			// Advanced section.
			$sections['advanced'] = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-settings-advanced.php';
		}

		/**
		 * Sanitize custom.
		 *
		 * @version 2.3.1
		 * @since   2.2.0
		 *
		 * @param mixed $value     The sanitized value.
		 * @param array $option    The option array.
		 * @param mixed $raw_value The raw value.
		 */
		public function sanitize_custom( $value, $option, $raw_value ) {
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
		 * Get settings.
		 *
		 * @version 2.0.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			return array_merge(
				apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				array(
					array(
						'title' => __( 'Reset Settings', 'product-notes-for-woocommerce' ),
						'type'  => 'title',
						'id'    => $this->id . '_' . $current_section . '_reset_options',
					),
					array(
						'title'    => __( 'Reset section settings', 'product-notes-for-woocommerce' ),
						'desc'     => '<strong>' . __( 'Reset', 'product-notes-for-woocommerce' ) . '</strong>',
						'desc_tip' => __( 'Check the box and save changes to reset.', 'product-notes-for-woocommerce' ),
						'id'       => $this->id . '_' . $current_section . '_reset',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => $this->id . '_' . $current_section . '_reset_options',
					),
				)
			);
		}

		/**
		 * Maybe reset settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function maybe_reset_settings() {
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
		 * Admin notices settings reset success.
		 *
		 * @version 3.2.0
		 * @since   1.0.0
		 */
		public function admin_notices_settings_reset_success() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
				esc_html__( 'Your settings have been reset.', 'product-notes-for-woocommerce' ) .
			'</strong></p></div>';
		}

		/**
		 * Save.
		 *
		 * @version 1.1.2
		 * @since   1.0.0
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
			do_action( 'alg_wc_product_note_settings_after_save' );
		}
	}

endif;

return new Alg_WC_PN_Settings();
