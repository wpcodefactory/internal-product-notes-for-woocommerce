<?php
/**
 * Product Notes for WooCommerce - Advanced Settings
 *
 * @version 2.9.5
 * @since   2.6.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Settings_Advanced' ) ) :

class Alg_WC_Product_Notes_Settings_Advanced {

	/**
	 * id.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $id;

	/**
	 * desc.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $desc;

	/**
	 * Constructor.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'product-notes-for-woocommerce' );
		add_filter( 'woocommerce_get_sections_alg_wc_product_notes',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_product_notes_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.9.0
	 * @since   2.6.0
	 *
	 * @todo    [next] (dev) Formatting Options: `admin_search_json` (will have to check `admin_search` as well)
	 * @todo    [next] (desc) Formatting Options: better desc?
	 */
	function get_settings() {

		$settings = array(
			array(
				'title'    => __( 'Advanced Options', 'product-notes-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pn_advanced_options',
			),
			array(
				'title'    => __( 'WPML', 'product-notes-for-woocommerce' ),
				'desc'     => __( 'Use default language product ID', 'product-notes-for-woocommerce' ),
				'id'       => 'alg_wc_pn_wpml_use_default_language_product_id',
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pn_advanced_options',
			),
		);

		$formatting_settings = array();
		$options = array(
			'do_shortcode'   => __( 'Process shortcodes', 'product-notes-for-woocommerce' ),
			'do_esc_html'    => __( 'Escape HTML', 'product-notes-for-woocommerce' ),
			'do_wpautop'     => __( 'Replace line breaks', 'product-notes-for-woocommerce' ),
			'make_clickable' => __( 'Convert plaintext URI to HTML links', 'product-notes-for-woocommerce' ),
			'glue'           => __( 'Notes glue', 'product-notes-for-woocommerce' ),
			'content'        => __( 'Content', 'product-notes-for-woocommerce' ),
		);
		$types = array(
			'product_tab'           => __( 'Product tab', 'product-notes-for-woocommerce' ),
			'product_meta'          => __( 'Product meta', 'product-notes-for-woocommerce' ),
			'single'                => __( 'Single product page', 'product-notes-for-woocommerce' ),
			'loop'                  => __( 'Shop pages', 'product-notes-for-woocommerce' ),
			'variation_description' => __( 'Variation description', 'product-notes-for-woocommerce' ),
			'cart'                  => __( 'Cart', 'product-notes-for-woocommerce' ),
			'products_column'       => __( 'Admin products list column', 'product-notes-for-woocommerce' ),
			'admin_orders'          => __( 'Admin orders', 'product-notes-for-woocommerce' ),
			'admin_emails'          => __( 'Admin emails', 'product-notes-for-woocommerce' ),
			'customer_emails'       => __( 'Customer emails', 'product-notes-for-woocommerce' ),
			'export'                => __( 'Export', 'product-notes-for-woocommerce' ),
			'wpo_wcpdf'             => __( 'WooCommerce PDF Invoices & Packing Slips', 'product-notes-for-woocommerce' ),
		);
		foreach ( $options as $option => $option_title ) {
			$i = 0;
			foreach ( $types as $type => $type_title ) {
				$default = alg_wc_pn()->core->formatter->get_default_args( $type );
				foreach ( array( 'private', 'public' ) as $private_or_public ) {
					$opt_suffix  = ( in_array( $type, array( 'single', 'loop' ) ) ? '_frontend_enabled' : '' );
					$opt_default = ( 'product_tab' === $type ? 'yes' : 'no' );
					if (
						'no' === get_option( "alg_wc_pn_{$private_or_public}_enabled", 'yes' ) ||
						'no' === get_option( "alg_wc_pn_{$private_or_public}_{$type}{$opt_suffix}", $opt_default )
					) {
						continue;
					}
					if ( in_array( $type, array( 'single', 'loop' ) ) && 'content' === $option ) {
						continue;
					}
					$i++;
					$id = ( 'product_tab' === $type ? "alg_wc_pn_{$private_or_public}_product_tab_options" : "alg_wc_pn_{$private_or_public}_{$type}_formatting_options" );
					$_settings = array(
						'title'    => ( 1 == $i ? $option_title : '' ),
						'desc'     => $type_title . ': <em>' . alg_wc_pn()->core->get_title( $private_or_public, 'plural' ) . '</em>',
						'id'       => "{$id}[{$option}]",
						'default'  => $default[ $option ],
						'type'     => ( 'glue' === $option ? 'text' : ( 'content' === $option ? 'textarea' : 'checkbox' ) ),
						'css'      => ( 'content' === $option ? 'width:100%;' : '' ),
					);
					if ( 'glue' === $option ) {
						$_settings['alg_wc_pn_sanitize'] = 'wp_kses_post';
					} elseif ( 'content' === $option ) {
						$_settings['desc_tip'] = sprintf( __( 'Placeholder: %s', 'product-notes-for-woocommerce' ), '<em>%product_notes%</em>' );
					} else {
						$_settings['checkboxgroup'] = ( 1 == $i ? 'start' : ( count( $options ) * count( $types ) * 2 == $i ? 'end' : '' ) );
					}
					$formatting_settings[] = $_settings;
				}
			}
		}
		if ( ! empty( $formatting_settings ) ) {
			$settings = array_merge( $settings,
				array(
					array(
						'title'    => __( 'Formatting Options', 'product-notes-for-woocommerce' ),
						'type'     => 'title',
						'id'       => 'alg_wc_pn_formatting_options',
					),
				),
				$formatting_settings,
				array(
					array(
						'type'     => 'sectionend',
						'id'       => 'alg_wc_pn_formatting_options',
					),
				)
			);
		}

		return $settings;
	}

}

endif;

return new Alg_WC_Product_Notes_Settings_Advanced();
