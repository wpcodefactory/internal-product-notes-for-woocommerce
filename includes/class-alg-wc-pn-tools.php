<?php
/**
 * Product Notes for WooCommerce - Tools Class
 *
 * @version 3.2.0
 * @since   2.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Tools' ) ) :

	/**
	 * Alg_WC_PN_Tools class.
	 *
	 * @version 3.2.0
	 * @since   2.0.0
	 */
	class Alg_WC_PN_Tools {

		/**
		 * Notice data.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var array
		 */
		public $notice_data;

		/**
		 * Constructor.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @todo (feature) export/import all product notes
		 * @todo (feature) add (same) note to all products
		 */
		public function __construct() {
			add_action( 'alg_wc_product_note_settings_after_save', array( $this, 'delete_all_notes' ) );
		}

		/**
		 * Delete all notes.
		 *
		 * @version 3.2.0
		 * @since   1.1.2
		 */
		public function delete_all_notes() {
			foreach ( array( 'private', 'public' ) as $private_or_public ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_tool_delete_all", 'no' ) ) {
					update_option( "alg_wc_pn_{$private_or_public}_tool_delete_all", 'no' );

					global $wpdb;
					$meta_key = '_' . alg_wc_pn()->get_id( $private_or_public );
					$deleted  = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->prepare(
							"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
							$meta_key
						)
					);

					$this->notice_data = array( 'deleted' => $deleted );
					add_action( 'admin_notices', array( $this, 'admin_notices_delete_all_notes' ), PHP_INT_MAX );
				}
			}
		}

		/**
		 * Admin notices delete all notes.
		 *
		 * @version 3.2.0
		 * @since   1.1.2
		 */
		public function admin_notices_delete_all_notes() {
			echo '<div class="notice notice-success is-dismissible"><p><strong>' .
				sprintf(
					/* Translators: %d: Number of products. */
					esc_html__( 'All notes have been deleted for %d product(s).', 'product-notes-for-woocommerce' ),
					absint( $this->notice_data['deleted'] )
				) .
			'</strong></p></div>';
		}
	}

endif;

return new Alg_WC_PN_Tools();
