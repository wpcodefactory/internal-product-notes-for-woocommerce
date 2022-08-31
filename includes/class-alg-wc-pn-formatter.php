<?php
/**
 * Product Notes for WooCommerce - Formatter Class
 *
 * @version 2.9.0
 * @since   2.7.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Formatter' ) ) :

class Alg_WC_Product_Notes_Formatter {

	/**
	 * Constructor.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_default_args.
	 *
	 * @version 2.9.0
	 * @since   2.7.0
	 */
	function get_default_args( $type ) {
		switch ( $type ) {
			case 'wpo_wcpdf':
				return array(
					'do_esc_html'    => 'yes',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'no',
					'glue'           => '<br>',
					'content'        => '<small>%product_notes%</small>',
				);
			case 'products_column':
				return array(
					'do_esc_html'    => 'yes',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '%product_notes%',
				);
			case 'product_meta':
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '<span class="posted_in">%product_notes%</span>',
				);
			case 'variation_description':
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '<p>%product_notes%</p>',
				);
			case 'cart':
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '<div>%product_notes%</div>',
				);
			case 'product_tab':
			case 'admin_orders':
				return array(
					'do_esc_html'    => 'yes',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'yes',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '%product_notes%',
				);
			case 'export':
			case 'admin_search_json':
				return array(
					'do_esc_html'    => 'yes',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'no',
					'glue'           => '; ',
					'content'        => '%product_notes%',
				);
			case 'admin_emails':
			case 'customer_emails':
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'no',
					'glue'           => '</li><li>',
					'content'        => '<ul class="wc-item-meta"><li>%product_notes%</li></ul>',
				);
			case 'single':
			case 'loop':
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '', // set separately
				);
			default: // just in case...
				return array(
					'do_esc_html'    => 'no',
					'do_shortcode'   => 'no',
					'do_wpautop'     => 'no',
					'make_clickable' => 'yes',
					'glue'           => '<br>',
					'content'        => '%product_notes%',
				);
		}
	}

	/**
	 * get_args.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 *
	 * @todo    [next] (feature) process images (i.e. similar to `make_clickable()`)
	 * @todo    [maybe] (feature) `$do_strip_tags`
	 */
	function get_args( $private_or_public, $type ) {
		$id      = ( 'product_tab' === $type ? "alg_wc_pn_{$private_or_public}_product_tab_options" : "alg_wc_pn_{$private_or_public}_{$type}_formatting_options" );
		$options = array_merge( $this->get_default_args( $type ), get_option( $id, array() ) );
		return array(
			'do_shortcode'   => filter_var( $options['do_shortcode'],   FILTER_VALIDATE_BOOLEAN ),
			'do_esc_html'    => filter_var( $options['do_esc_html'],    FILTER_VALIDATE_BOOLEAN ),
			'do_wpautop'     => filter_var( $options['do_wpautop'],     FILTER_VALIDATE_BOOLEAN ),
			'make_clickable' => filter_var( $options['make_clickable'], FILTER_VALIDATE_BOOLEAN ),
			'glue'           => $options['glue'],
			'content'        => $options['content'],
		);
	}

	/**
	 * format_notes.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function format_notes( $notes, $args = array() ) {
		$args = array_merge( array(
				'do_esc_html'    => true,
				'do_shortcode'   => false,
				'glue'           => '<br>',
				'do_wpautop'     => true,
				'make_clickable' => false,
				'content'        => '%product_notes%',
			), $args );
		$notes = ( $args['do_shortcode'] ? array_map( 'do_shortcode', $notes ) : $notes );
		$notes = ( $args['do_esc_html'] ? array_map( 'esc_html', $notes ) : $notes );
		$notes = implode( $args['glue'], $notes );
		$notes = ( $args['make_clickable'] ? make_clickable( $notes ) : $notes );
		$notes = ( $args['do_wpautop'] ? wpautop( $notes ) : $notes );
		$notes = str_replace( '%product_notes%', $notes, $args['content'] );
		return $notes;
	}

}

endif;

return new Alg_WC_Product_Notes_Formatter();
