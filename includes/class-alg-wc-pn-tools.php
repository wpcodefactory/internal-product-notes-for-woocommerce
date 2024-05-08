<?php
/**
 * Product Notes for WooCommerce - Tools Class
 *
 * @version 2.9.5
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Tools' ) ) :

class Alg_WC_Product_Notes_Tools {

	/**
	 * notice_data.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $notice_data;

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (feature) export/import all product notes
	 * @todo    [next] (feature) add (same) note to all products
	 */
	function __construct() {
		add_action( 'alg_wc_product_note_settings_after_save', array( $this, 'delete_all_notes' ) );
	}

	/**
	 * delete_all_notes.
	 *
	 * @version 2.8.0
	 * @since   1.1.2
	 */
	function delete_all_notes() {
		foreach ( array( 'private', 'public' ) as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_tool_delete_all", 'no' ) ) {
				update_option( "alg_wc_pn_{$private_or_public}_tool_delete_all", 'no' );
				global $wpdb;
				$meta_key          = '_' . alg_wc_pn()->get_id( $private_or_public );
				$deleted           = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '{$meta_key}'" );
				$this->notice_data = array( 'deleted' => $deleted );
				add_action( 'admin_notices', array( $this, 'admin_notices_delete_all_notes' ), PHP_INT_MAX );
			}
		}
	}

	/**
	 * admin_notices_delete_all_notes.
	 *
	 * @version 2.8.0
	 * @since   1.1.2
	 */
	function admin_notices_delete_all_notes() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			sprintf( __( 'All notes have been deleted for %d product(s).', 'product-notes-for-woocommerce' ), $this->notice_data['deleted'] ) .
		'</strong></p></div>';
	}

}

endif;

return new Alg_WC_Product_Notes_Tools();
