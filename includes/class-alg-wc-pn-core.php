<?php
/**
 * Product Notes for WooCommerce - Core Class
 *
 * @version 2.9.5
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Core' ) ) :

class Alg_WC_Product_Notes_Core {

	/**
	 * wpml_use_default_language.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $wpml_use_default_language;

	/**
	 * formatter.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $formatter;

	/**
	 * Constructor.
	 *
	 * @version 2.7.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (feature) add `[alg_wc_pn_translate]` shortcode (must apply `do_shortcode()` in the output then)
	 * @todo    [next] [!] (dev) variations in backend: admin products list, export, import, quick edit, bulk edit?
	 * @todo    [next] [!] (dev) variations in frontend (e.g. product tab)
	 * @todo    [next] (feature) frontend search
	 * @todo    [next] (feature) show in cart
	 * @todo    [maybe] use custom post type for notes (i.e. instead of storing it in product meta)
	 * @todo    [maybe] option to add notes to *customer* emails
	 * @todo    [maybe] option to add notes to order details (e.g. "Order received" page)
	 */
	function __construct() {
		$this->wpml_use_default_language = ( 'yes' === get_option( 'alg_wc_pn_wpml_use_default_language_product_id', 'no' ) );
		$this->formatter = require_once( 'class-alg-wc-pn-formatter.php' );
		require_once( 'alg-wc-pn-functions.php' );
		require_once( 'class-alg-wc-pn-edit.php' );
		require_once( 'class-alg-wc-pn-display-frontend.php' );
		require_once( 'class-alg-wc-pn-tools.php' );
		do_action( 'alg_wc_pn_core_loaded', $this );
	}

	/**
	 * get_title.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_title( $private_or_public, $singular_or_plural ) {
		$count = ( 'plural' === $singular_or_plural ? 2 : 1 );
		return ( 'private' === $private_or_public ?
			_n( 'Private note', 'Private notes', $count, 'product-notes-for-woocommerce' ) :
			_n( 'Public note',  'Public notes',  $count, 'product-notes-for-woocommerce' ) );
	}

	/**
	 * get_product_notes.
	 *
	 * @version 2.6.0
	 * @since   2.0.0
	 */
	function get_product_notes( $private_or_public, $product_id = 0 ) {
		if ( 0 == $product_id ) {
			$product_id = $this->get_product_id();
		}
		$product_notes = get_post_meta( $product_id, '_' . alg_wc_pn()->get_id( $private_or_public ), true );
		return apply_filters( 'alg_wc_pn_get_product_notes', ( '' !== $product_notes ? $product_notes : array() ), $private_or_public, $product_id );
	}

	/**
	 * get_product_note_values.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] optional `check_empty`
	 */
	function get_product_note_values( $private_or_public, $product_id = 0 ) {
		$result = $this->get_product_notes( $private_or_public, $product_id );
		$result = array_filter( wp_list_pluck( $result, 'value' ), array( $this, 'check_empty' ) );
		return apply_filters( 'alg_wc_pn_get_product_note_values', $result, $private_or_public, $product_id );
	}

	/**
	 * check_empty.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function check_empty( $value ) {
		return ( '' !== $value );
	}

	/**
	 * set_product_notes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function set_product_notes( $notes, $private_or_public, $product_id, $del = array() ) {
		$old_notes = $this->get_product_notes( $private_or_public, $product_id );
		$result    = array();
		$_note_id  = 0;
		foreach ( $notes as $note_id => $note ) {
			if ( ! isset( $del[ $note_id ] ) ) {
				if ( ! empty( $old_notes[ $note_id ] ) && $note === addslashes( $old_notes[ $note_id ]['value'] ) ) {
					// No changes
					$result[ $_note_id ] = $old_notes[ $note_id ];
				} else {
					// New note
					$result[ $_note_id ] = $this->generate_note_data( $note );
				}
				$_note_id++;
			}
		}
		update_post_meta( $product_id, '_' . alg_wc_pn()->get_id( $private_or_public ), $result );
	}

	/**
	 * append_product_note.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function append_product_note( $note, $private_or_public, $product_id ) {
		$notes = $this->get_product_notes( $private_or_public, $product_id );
		array_unshift( $notes, $this->generate_note_data( $note ) );
		update_post_meta( $product_id, '_' . alg_wc_pn()->get_id( $private_or_public ), $notes );
	}

	/**
	 * generate_note_data.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function generate_note_data( $note ) {
		return array(
			'time'   => ( int ) current_time( 'timestamp' ),
			'author' => get_current_user_id(),
			'value'  => $note,
		);
	}

	/**
	 * sanitize_note.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function sanitize_note( $str ) {
		return wp_kses_post( trim( $str ) );
	}

	/**
	 * get_product_id.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 *
	 * @see     https://wpml.org/documentation/support/creating-multilingual-wordpress-themes/language-dependent-ids/
	 * @see     https://wpml.org/forums/topic/api-to-get-the-default-language/
	 *
	 * @todo    [next] (dev) WPML: search for `$product_id`, `$post_id`, `$variation_id` (and maybe `$id`)
	 * @todo    [next] (dev) Polylang
	 */
	function get_product_id( $product = false ) {
		$product_id = ( $product ? $product->get_id() : get_the_ID() );
		// WPML
		if ( $this->wpml_use_default_language ) {
			global $sitepress;
			if ( $sitepress ) {
				$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $sitepress->get_default_language() );
			}
		}
		// Result
		return $product_id;
	}

}

endif;

return new Alg_WC_Product_Notes_Core();
