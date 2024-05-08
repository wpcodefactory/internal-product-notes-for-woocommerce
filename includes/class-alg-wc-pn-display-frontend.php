<?php
/**
 * Product Notes for WooCommerce - Display Class
 *
 * @version 2.9.5
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Display_Frontend' ) ) :

class Alg_WC_Product_Notes_Display_Frontend {

	/**
	 * is_user_admin.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $is_user_admin;

	/**
	 * content.
	 *
	 * @version 2.9.5
	 * @since   2.9.5
	 */
	public $content;

	/**
	 * Constructor.
	 *
	 * @version 2.9.0
	 * @since   2.0.0
	 *
	 * @todo    [next] Code refactoring: merge "product meta" with  "single/loop"
	 * @todo    [next] `alg_wc_pn_public_logged_in_user_only`: optionally display "You have to log in" message
	 */
	function __construct() {

		// Product tab
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tabs' ) );

		// Product meta, Single, Loop
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_meta", 'no' ) ) {
				$product_meta_hook = get_option( "alg_wc_pn_{$private_or_public}_product_meta_position", 'woocommerce_product_meta_end' );
				add_action( $product_meta_hook, array( $this, "display_in_product_meta_{$private_or_public}" ) );
			}
			foreach ( array( 'single', 'loop' ) as $single_or_loop ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_enabled", 'no' ) ) {
					$hooks      = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_position", array() );
					$priorities = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_priority", array() );
					foreach ( $hooks as $hook ) {
						$priority = ( isset( $priorities[ $hook ] ) ? $priorities[ $hook ] : 10 );
						add_action( $hook, array( $this, "display_{$private_or_public}_{$single_or_loop}" ), $priority );
					}
				}
			}
		}

		// Variations
		add_filter( 'woocommerce_available_variation', array( $this, 'variation_description' ), 10, 3 );

		// Cart
		add_action( 'woocommerce_after_cart_item_name', array( $this, 'display_in_cart' ) );

	}

	/**
	 * display_in_cart.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	function display_in_cart( $cart_item ) {
		$product_id = ( ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'] );
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if (
				'yes' === get_option( "alg_wc_pn_{$private_or_public}_cart", 'no' ) &&
				$this->do_display( $private_or_public ) &&
				array() != alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id )
			) {
				echo alg_wc_pn_get_product_notes( $private_or_public, $product_id, alg_wc_pn()->core->formatter->get_args( $private_or_public, 'cart' ) );
			}
		}
	}

	/**
	 * variation_description.
	 *
	 * @version 2.7.0
	 * @since   2.5.0
	 *
	 * @todo    [next] (dev) WPML: `$variation->get_id()` x 2
	 */
	function variation_description( $args, $product, $variation ) {
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if (
				'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) &&
				'yes' === get_option( "alg_wc_pn_{$private_or_public}_variation_description", 'no' ) &&
				$this->do_display( $private_or_public ) &&
				array() != alg_wc_pn()->core->get_product_note_values( $private_or_public, $variation->get_id() )
			) {
				if ( ! isset( $args['variation_description'] ) ) {
					$args['variation_description'] = '';
				}
				$args['variation_description'] .= alg_wc_pn_get_product_notes( $private_or_public, $variation->get_id(), alg_wc_pn()->core->formatter->get_args( $private_or_public, 'variation_description' ) );
			}
		}
		return $args;
	}

	/**
	 * current_user_is_admin.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [later] customizable user capability, i.e., `manage_woocommerce`
	 */
	function current_user_is_admin() {
		if ( ! isset( $this->is_user_admin ) ) {
			$this->is_user_admin = ( function_exists( 'current_user_can' ) && current_user_can( 'manage_woocommerce' ) );
		}
		return $this->is_user_admin;
	}

	/**
	 * do_display.
	 *
	 * @version 2.9.2
	 * @since   2.3.0
	 */
	function do_display( $private_or_public ) {
		switch ( $private_or_public ) {
			case 'public':
				$res = ( 'no' === get_option( 'alg_wc_pn_public_logged_in_user_only', 'no' ) || is_user_logged_in() );
				break;
			default: // 'private'
				$res = $this->current_user_is_admin();
		}
		return apply_filters( 'alg_wc_pn_do_display', $res, $private_or_public );
	}

	/**
	 * display.
	 *
	 * @version 2.7.0
	 * @since   2.3.0
	 *
	 * @todo    [maybe] add date and author?
	 */
	function display( $private_or_public, $single_or_loop ) {
		if ( ! $this->do_display( $private_or_public ) ) {
			return;
		}
		global $product;
		$product_id = alg_wc_pn()->core->get_product_id( $product );
		if ( ! isset( $this->content[ $single_or_loop ] ) ) {
			$this->content[ $single_or_loop ] = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_content", array() );
		}
		$filter                     = current_filter();
		$formatting_args            = alg_wc_pn()->core->formatter->get_args( $private_or_public, $single_or_loop );
		$formatting_args['content'] = ( isset( $this->content[ $single_or_loop ][ $filter ] ) ? $this->content[ $single_or_loop ][ $filter ] : '<p>%product_notes%</p>' );
		echo alg_wc_pn_get_product_notes( $private_or_public, $product_id, $formatting_args );
	}

	/**
	 * display_private_loop.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function display_private_loop() {
		$this->display( 'private', 'loop' );
	}

	/**
	 * display_public_loop.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function display_public_loop() {
		$this->display( 'public', 'loop' );
	}

	/**
	 * display_private_single.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function display_private_single() {
		$this->display( 'private', 'single' );
	}

	/**
	 * display_public_single.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function display_public_single() {
		$this->display( 'public', 'single' );
	}

	/**
	 * display_in_product_meta_private.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function display_in_product_meta_private() {
		$this->display_in_product_meta( 'private' );
	}

	/**
	 * display_in_product_meta_public.
	 *
	 * @version 2.3.0
	 * @since   2.2.0
	 */
	function display_in_product_meta_public() {
		$this->display_in_product_meta( 'public' );
	}

	/**
	 * display_in_product_meta.
	 *
	 * @version 2.7.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] add date and author
	 */
	function display_in_product_meta( $private_or_public ) {
		if ( ! $this->do_display( $private_or_public ) ) {
			return;
		}
		global $product;
		$product_id = alg_wc_pn()->core->get_product_id( $product );
		echo alg_wc_pn_get_product_notes( $private_or_public, $product_id, alg_wc_pn()->core->formatter->get_args( $private_or_public, 'product_meta' ) );
	}

	/**
	 * add_product_tabs.
	 *
	 * @version 2.6.0
	 * @since   1.0.0
	 *
	 * @todo    [next] customizable tab IDs
	 * @todo    [next] JS (i.e. when opened by link, tab should automatically open)
	 * @todo    [maybe] per product: tab id
	 * @todo    [maybe] per product: tab priority
	 * @todo    [maybe] per product: tab enable/disable
	 */
	function add_product_tabs( $tabs ) {
		global $product;
		$product_id = alg_wc_pn()->core->get_product_id( $product );
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab", 'yes' ) ) {
				if ( array() != ( $value = alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id ) ) ) {
					if ( ! $this->do_display( $private_or_public ) ) {
						continue;
					}
					$tab_id               = ( 'private' === $private_or_public ? 'private_notes' : 'notes' );
					$default_tab_title    = ( 'private' === $private_or_public ? __( 'Private notes', 'product-notes-for-woocommerce' ) : __( 'Notes', 'product-notes-for-woocommerce' ) );
					$default_tab_priority = ( 'private' === $private_or_public ? 100 : 101 );
					$tab_title            = ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) &&
						'' !== ( $tab_title_per_product = get_post_meta( $product_id, alg_wc_pn()->get_id( $private_or_public ) . '_tab_title' , true ) ) ?
							$tab_title_per_product : get_option( "alg_wc_pn_{$private_or_public}_product_tab_title", $default_tab_title ) );
					$tabs[ $tab_id ] = array(
						'title'    => $tab_title,
						'priority' => get_option( "alg_wc_pn_{$private_or_public}_product_tab_priority", $default_tab_priority ),
						'callback' => array( $this, "display_product_tab_{$private_or_public}" ),
					);
				}
			}
		}
		return $tabs;
	}

	/**
	 * display_product_tab.
	 *
	 * @version 2.7.0
	 * @since   2.2.0
	 *
	 * @todo    [maybe] add date and author?
	 */
	function display_product_tab( $private_or_public ) {
		global $product;
		$product_id = alg_wc_pn()->core->get_product_id( $product );
		$notes      = alg_wc_pn_get_product_notes( $private_or_public, $product_id, alg_wc_pn()->core->formatter->get_args( $private_or_public, 'product_tab' ) );
		echo apply_filters( 'alg_wc_pn_product_tab', $notes, $private_or_public, $product_id );
	}

	/**
	 * display_product_tab_private.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 */
	function display_product_tab_private() {
		$this->display_product_tab( 'private' );
	}

	/**
	 * display_product_tab_public.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 */
	function display_product_tab_public() {
		$this->display_product_tab( 'public' );
	}

}

endif;

return new Alg_WC_Product_Notes_Display_Frontend();
