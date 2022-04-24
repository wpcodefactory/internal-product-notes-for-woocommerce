<?php
/**
 * Product Notes for WooCommerce - Functions
 *
 * @version 2.7.0
 * @since   1.1.3
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_pn_get_enabled_sections' ) ) {
	/**
	 * alg_wc_pn_get_enabled_sections.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function alg_wc_pn_get_enabled_sections() {
		$sections = array();
		foreach ( array( 'private', 'public' ) as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_enabled", 'yes' ) ) {
				$sections[] = $private_or_public;
			}
		}
		return $sections;
	}
}

if ( ! function_exists( 'alg_wc_pn_is_any_section_enabled' ) ) {
	/**
	 * alg_wc_pn_is_any_section_enabled.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function alg_wc_pn_is_any_section_enabled() {
		$sections = alg_wc_pn_get_enabled_sections();
		return ( ! empty( $sections ) );
	}
}

if ( ! function_exists( 'alg_wc_pn_has_product_notes' ) ) {
	/**
	 * alg_wc_pn_has_product_notes.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 *
	 * @todo    [next] use it everywhere
	 * @todo    [next] do we really need `function_exists( 'alg_wc_pn' )`?
	 */
	function alg_wc_pn_has_product_notes( $private_or_public, $product_id = 0 ) {
		return ( function_exists( 'alg_wc_pn' ) && array() != alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id ) );
	}
}

if ( ! function_exists( 'alg_wc_pn_get_product_notes' ) ) {
	/**
	 * alg_wc_pn_get_product_notes.
	 *
	 * @version 2.7.0
	 * @since   1.1.3
	 *
	 * @todo    [next] all notes for the **order**
	 */
	function alg_wc_pn_get_product_notes( $private_or_public, $product_id = 0, $args = array() ) {
		$notes = '';
		if ( function_exists( 'alg_wc_pn' ) ) {
			$notes = alg_wc_pn()->core->formatter->format_notes( alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id ), $args );
		}
		return $notes;
	}
}

if ( ! function_exists( 'alg_wc_product_notes_shortcode' ) ) {
	/**
	 * alg_wc_product_notes_shortcode.
	 *
	 * @version 2.7.0
	 * @since   1.1.3
	 *
	 * @todo    [next] (dev) use `$content`
	 */
	function alg_wc_product_notes_shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts( array(
			'private_or_public' => 'public',
			'product_id'        => 0,
			'glue'              => '<br>',
			'content'           => '%product_notes%',
			'do_shortcode'      => false,
			'do_esc_html'       => true,
			'do_wpautop'        => true,
			'make_clickable'    => true,
		), $atts, 'alg_wc_product_notes_shortcode' );
		return alg_wc_pn_get_product_notes( $atts['private_or_public'], $atts['product_id'], array(
			'glue'           => $atts['glue'],
			'content'        => $atts['content'],
			'do_shortcode'   => filter_var( $atts['do_shortcode'],   FILTER_VALIDATE_BOOLEAN ),
			'do_esc_html'    => filter_var( $atts['do_esc_html'],    FILTER_VALIDATE_BOOLEAN ),
			'do_wpautop'     => filter_var( $atts['do_wpautop'],     FILTER_VALIDATE_BOOLEAN ),
			'make_clickable' => filter_var( $atts['make_clickable'], FILTER_VALIDATE_BOOLEAN ),
		) );
	}
	add_shortcode( 'alg_wc_product_notes', 'alg_wc_product_notes_shortcode' );
}
