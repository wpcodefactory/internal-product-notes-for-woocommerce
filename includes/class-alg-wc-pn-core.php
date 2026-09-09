<?php
/**
 * Product Notes for WooCommerce - Core Class
 *
 * @version 3.2.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Core' ) ) :

	/**
	 * Alg_WC_PN_Core class.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 */
	class Alg_WC_PN_Core {

		/**
		 * WPML use default language.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var bool
		 */
		public $wpml_use_default_language;

		/**
		 * Formatter.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var Alg_WC_PN_Formatter
		 */
		public $formatter;

		/**
		 * Constructor.
		 *
		 * @version 3.1.1
		 * @since   1.0.0
		 *
		 * @todo (feature) add `[alg_wc_pn_translate]` shortcode (must apply `do_shortcode()` in the output then)
		 * @todo (dev) variations in backend: admin products list, export, import, quick edit, bulk edit?
		 * @todo (dev) variations in frontend (e.g., product tab)
		 * @todo (feature) frontend search
		 * @todo (feature) show in cart
		 * @todo (dev) use custom post type for notes (i.e., instead of storing it in product meta)
		 * @todo (dev) option to add notes to *customer* emails
		 * @todo (dev) option to add notes to order details (e.g., "Order received" page)
		 */
		public function __construct() {
			$this->wpml_use_default_language = ( 'yes' === get_option( 'alg_wc_pn_wpml_use_default_language_product_id', 'no' ) );

			$this->formatter = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-formatter.php';

			require_once plugin_dir_path( __FILE__ ) . 'alg-wc-pn-functions.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-edit.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-display-frontend.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pn-tools.php';

			do_action( 'alg_wc_pn_core_loaded', $this );
		}

		/**
		 * Get title.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param string $private_or_public Private or public.
		 * @param string $singular_or_plural Singular or plural.
		 */
		public function get_title( $private_or_public, $singular_or_plural ) {
			$count = ( 'plural' === $singular_or_plural ? 2 : 1 );
			return (
				'private' === $private_or_public ?
				_n( 'Private note', 'Private notes', $count, 'product-notes-for-woocommerce' ) :
				_n( 'Public note', 'Public notes', $count, 'product-notes-for-woocommerce' )
			);
		}

		/**
		 * Get product notes.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 *
		 * @param string $private_or_public Private or public.
		 * @param int    $product_id        Product ID.
		 */
		public function get_product_notes( $private_or_public, $product_id = 0 ) {
			if ( 0 === absint( $product_id ) ) {
				$product_id = $this->get_product_id();
			}
			$product_notes = get_post_meta(
				$product_id,
				'_' . alg_wc_pn()->get_id( $private_or_public ),
				true
			);
			return apply_filters(
				'alg_wc_pn_get_product_notes',
				( '' !== $product_notes ? $product_notes : array() ),
				$private_or_public,
				$product_id
			);
		}

		/**
		 * Get product note values.
		 *
		 * @version 2.2.0
		 * @since   2.0.0
		 *
		 * @param string $private_or_public Private or public.
		 * @param int    $product_id        Product ID.
		 *
		 * @todo (dev) optional `check_empty`
		 */
		public function get_product_note_values( $private_or_public, $product_id = 0 ) {
			$result = $this->get_product_notes( $private_or_public, $product_id );
			$result = array_filter( wp_list_pluck( $result, 'value' ), array( $this, 'check_empty' ) );
			return apply_filters( 'alg_wc_pn_get_product_note_values', $result, $private_or_public, $product_id );
		}

		/**
		 * Check empty.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param string $value Value to check.
		 */
		public function check_empty( $value ) {
			return ( '' !== $value );
		}

		/**
		 * Set product notes.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param array  $notes             Notes to set.
		 * @param string $private_or_public Private or public.
		 * @param int    $product_id        Product ID.
		 * @param array  $del               Notes to delete.
		 */
		public function set_product_notes( $notes, $private_or_public, $product_id, $del = array() ) {
			$old_notes = $this->get_product_notes( $private_or_public, $product_id );
			$result    = array();
			$_note_id  = 0;
			foreach ( $notes as $note_id => $note ) {
				if ( ! isset( $del[ $note_id ] ) ) {
					if (
						! empty( $old_notes[ $note_id ] ) &&
						addslashes( $old_notes[ $note_id ]['value'] ) === $note
					) {
						// No changes.
						$result[ $_note_id ] = $old_notes[ $note_id ];
					} else {
						// New note.
						$result[ $_note_id ] = $this->generate_note_data( $note );
					}
					++$_note_id;
				}
			}
			update_post_meta( $product_id, '_' . alg_wc_pn()->get_id( $private_or_public ), $result );
		}

		/**
		 * Append product note.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param string $note              Note to append.
		 * @param string $private_or_public Private or public.
		 * @param int    $product_id        Product ID.
		 */
		public function append_product_note( $note, $private_or_public, $product_id ) {
			$notes = $this->get_product_notes( $private_or_public, $product_id );
			array_unshift( $notes, $this->generate_note_data( $note ) );
			update_post_meta( $product_id, '_' . alg_wc_pn()->get_id( $private_or_public ), $notes );
		}

		/**
		 * Generate note data.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param string $note Note content.
		 */
		public function generate_note_data( $note ) {
			return array(
				'time'   => (int) current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				'author' => get_current_user_id(),
				'value'  => $note,
			);
		}

		/**
		 * Sanitize note.
		 *
		 * @version 2.3.1
		 * @since   2.3.1
		 *
		 * @param string $str Note content to sanitize.
		 */
		public function sanitize_note( $str ) {
			return wp_kses_post( trim( $str ) );
		}

		/**
		 * Get product ID.
		 *
		 * @version 2.6.0
		 * @since   2.6.0
		 *
		 * @param WC_Product|false $product Product object or false to use the current post ID.
		 *
		 * @see https://wpml.org/documentation/support/creating-multilingual-wordpress-themes/language-dependent-ids/
		 * @see https://wpml.org/forums/topic/api-to-get-the-default-language/
		 *
		 * @todo (dev) WPML: search for `$product_id`, `$post_id`, `$variation_id` (and maybe `$id`)
		 * @todo (dev) Polylang
		 */
		public function get_product_id( $product = false ) {
			$product_id = ( $product ? $product->get_id() : get_the_ID() );

			// WPML.
			if ( $this->wpml_use_default_language ) {
				global $sitepress;
				if ( $sitepress ) {
					$product_id = apply_filters(
						'wpml_object_id', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
						$product_id,
						'product',
						true,
						$sitepress->get_default_language()
					);
				}
			}

			// Result.
			return $product_id;
		}
	}

endif;

return new Alg_WC_PN_Core();
