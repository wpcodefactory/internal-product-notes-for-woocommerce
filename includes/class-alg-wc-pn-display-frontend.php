<?php
/**
 * Product Notes for WooCommerce - Display Class
 *
 * @version 3.2.0
 * @since   2.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Display_Frontend' ) ) :

	/**
	 * Alg_WC_PN_Display_Frontend class.
	 *
	 * @version 3.2.0
	 * @since   2.0.0
	 */
	class Alg_WC_PN_Display_Frontend {

		/**
		 * Is user admin.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var bool
		 */
		public $is_user_admin;

		/**
		 * Content.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var array
		 */
		public $content;

		/**
		 * Constructor.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 *
		 * @todo (dev) Code refactoring: merge "product meta" with  "single/loop"
		 * @todo (dev) `alg_wc_pn_public_logged_in_user_only`: optionally display "You have to log in" message
		 */
		public function __construct() {
			// Product tab.
			add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tabs' ) );

			// Product meta, Single, Loop.
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_meta", 'no' ) ) {
					$product_meta_hook = get_option(
						"alg_wc_pn_{$private_or_public}_product_meta_position",
						'woocommerce_product_meta_end'
					);
					add_action(
						$product_meta_hook,
						array( $this, "display_in_product_meta_{$private_or_public}" )
					);
				}
				foreach ( array( 'single', 'loop' ) as $single_or_loop ) {
					if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_enabled", 'no' ) ) {
						$hooks      = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_position", array() );
						$priorities = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_priority", array() );
						foreach ( $hooks as $hook ) {
							$priority = ( $priorities[ $hook ] ?? 10 );
							add_action(
								$hook,
								array( $this, "display_{$private_or_public}_{$single_or_loop}" ),
								$priority
							);
						}
					}
				}
			}

			// Variations.
			add_filter( 'woocommerce_available_variation', array( $this, 'variation_description' ), 10, 3 );

			// Cart.
			add_action( 'woocommerce_after_cart_item_name', array( $this, 'display_in_cart' ) );

			// Checkout.
			add_action( 'woocommerce_cart_item_name', array( $this, 'display_in_checkout' ) );
		}

		/**
		 * Display in checkout.
		 *
		 * @version 3.2.0
		 * @since   3.1.0
		 *
		 * @param string $item_name The item name.
		 * @param array  $cart_item The cart item.
		 */
		public function display_in_checkout( $item_name, $cart_item ) {
			if ( ! is_checkout() ) {
				return $item_name;
			}
			$product_id = ( ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'] );
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if (
					'yes' === get_option( "alg_wc_pn_{$private_or_public}_checkout", 'no' ) &&
					$this->do_display( $private_or_public ) &&
					array() !== alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id )
				) {
					$item_name .= alg_wc_pn_get_product_notes(
						$private_or_public,
						$product_id,
						alg_wc_pn()->core->formatter->get_args(
							$private_or_public,
							'checkout'
						)
					);
				}
			}
			return $item_name;
		}

		/**
		 * Display in cart.
		 *
		 * @version 3.2.0
		 * @since   2.9.0
		 *
		 * @param array $cart_item The cart item.
		 */
		public function display_in_cart( $cart_item ) {
			$product_id = ( ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'] );
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if (
					'yes' === get_option( "alg_wc_pn_{$private_or_public}_cart", 'no' ) &&
					$this->do_display( $private_or_public ) &&
					array() !== alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id )
				) {
					echo wp_kses_post(
						alg_wc_pn_get_product_notes(
							$private_or_public,
							$product_id,
							alg_wc_pn()->core->formatter->get_args(
								$private_or_public,
								'cart'
							)
						)
					);
				}
			}
		}

		/**
		 * Variation description.
		 *
		 * @version 3.2.0
		 * @since   2.5.0
		 *
		 * @param array  $args       The variation args.
		 * @param object $product    The product object.
		 * @param object $variation  The variation object.
		 *
		 * @todo (dev) WPML: `$variation->get_id()` x 2
		 */
		public function variation_description( $args, $product, $variation ) {
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if (
					'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) &&
					'yes' === get_option( "alg_wc_pn_{$private_or_public}_variation_description", 'no' ) &&
					$this->do_display( $private_or_public ) &&
					array() !== alg_wc_pn()->core->get_product_note_values( $private_or_public, $variation->get_id() )
				) {
					if ( ! isset( $args['variation_description'] ) ) {
						$args['variation_description'] = '';
					}
					$args['variation_description'] .= alg_wc_pn_get_product_notes(
						$private_or_public,
						$variation->get_id(),
						alg_wc_pn()->core->formatter->get_args(
							$private_or_public,
							'variation_description'
						)
					);
				}
			}
			return $args;
		}

		/**
		 * Current user is admin.
		 *
		 * @version 2.0.0
		 * @since   1.0.0
		 *
		 * @todo (dev) customizable user capability, i.e., `manage_woocommerce`
		 */
		public function current_user_is_admin() {
			if ( ! isset( $this->is_user_admin ) ) {
				$this->is_user_admin = (
					function_exists( 'current_user_can' ) &&
					current_user_can( 'manage_woocommerce' ) // phpcs:ignore WordPress.WP.Capabilities.Unknown
				);
			}
			return $this->is_user_admin;
		}

		/**
		 * Do display.
		 *
		 * @version 2.9.2
		 * @since   2.3.0
		 *
		 * @param string $private_or_public Private or public.
		 */
		public function do_display( $private_or_public ) {
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
		 * Display.
		 *
		 * @version 3.2.0
		 * @since   2.3.0
		 *
		 * @param string $private_or_public Private or public.
		 * @param string $single_or_loop    Single or loop.
		 *
		 * @todo (dev) add date and author?
		 */
		public function display( $private_or_public, $single_or_loop ) {
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
			$formatting_args['content'] = ( $this->content[ $single_or_loop ][ $filter ] ?? '<p>%product_notes%</p>' );

			echo wp_kses_post(
				alg_wc_pn_get_product_notes(
					$private_or_public,
					$product_id,
					$formatting_args
				)
			);
		}

		/**
		 * Display private loop.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function display_private_loop() {
			$this->display( 'private', 'loop' );
		}

		/**
		 * Display public loop.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function display_public_loop() {
			$this->display( 'public', 'loop' );
		}

		/**
		 * Display private single.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function display_private_single() {
			$this->display( 'private', 'single' );
		}

		/**
		 * Display public single.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function display_public_single() {
			$this->display( 'public', 'single' );
		}

		/**
		 * Display in product meta private.
		 *
		 * @version 2.3.0
		 * @since   2.2.0
		 */
		public function display_in_product_meta_private() {
			$this->display_in_product_meta( 'private' );
		}

		/**
		 * Display in product meta public.
		 *
		 * @version 2.3.0
		 * @since   2.2.0
		 */
		public function display_in_product_meta_public() {
			$this->display_in_product_meta( 'public' );
		}

		/**
		 * Display in product meta.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 *
		 * @param string $private_or_public Private or public.
		 *
		 * @todo (dev) add date and author
		 */
		public function display_in_product_meta( $private_or_public ) {
			if ( ! $this->do_display( $private_or_public ) ) {
				return;
			}

			global $product;
			$product_id = alg_wc_pn()->core->get_product_id( $product );

			echo wp_kses_post(
				alg_wc_pn_get_product_notes(
					$private_or_public,
					$product_id,
					alg_wc_pn()->core->formatter->get_args(
						$private_or_public,
						'product_meta'
					)
				)
			);
		}

		/**
		 * Add product tabs.
		 *
		 * @version 3.2.0
		 * @since   1.0.0
		 *
		 * @param array $tabs Array of existing product tabs.
		 *
		 * @todo (dev) customizable tab IDs
		 * @todo (dev) JS (i.e., when opened by link, tab should automatically open)
		 * @todo (dev) per product: tab id
		 * @todo (dev) per product: tab priority
		 * @todo (dev) per product: tab enable/disable
		 */
		public function add_product_tabs( $tabs ) {
			global $product;
			$product_id = alg_wc_pn()->core->get_product_id( $product );
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab", 'yes' ) ) {
					if ( array() !== alg_wc_pn()->core->get_product_note_values( $private_or_public, $product_id ) ) {
						if ( ! $this->do_display( $private_or_public ) ) {
							continue;
						}
						$tab_id                = (
							'private' === $private_or_public ?
							'private_notes' :
							'notes'
						);
						$default_tab_title     = (
							'private' === $private_or_public ?
							__( 'Private notes', 'product-notes-for-woocommerce' ) :
							__( 'Notes', 'product-notes-for-woocommerce' )
						);
						$default_tab_priority  = (
							'private' === $private_or_public ?
							100 :
							101
						);
						$tab_title_per_product = get_post_meta( $product_id, alg_wc_pn()->get_id( $private_or_public ) . '_tab_title', true );
						$tab_title             = (
							(
								'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) &&
								'' !== $tab_title_per_product
							) ?
							$tab_title_per_product :
							get_option( "alg_wc_pn_{$private_or_public}_product_tab_title", $default_tab_title )
						);
						$tabs[ $tab_id ]       = array(
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
		 * Display product tab.
		 *
		 * @version 3.2.0
		 * @since   2.2.0
		 *
		 * @param string $private_or_public Private or public.
		 *
		 * @todo (dev) add date and author?
		 */
		public function display_product_tab( $private_or_public ) {
			global $product;
			$product_id = alg_wc_pn()->core->get_product_id( $product );
			$notes      = alg_wc_pn_get_product_notes(
				$private_or_public,
				$product_id,
				alg_wc_pn()->core->formatter->get_args(
					$private_or_public,
					'product_tab'
				)
			);

			echo wp_kses_post(
				apply_filters(
					'alg_wc_pn_product_tab',
					$notes,
					$private_or_public,
					$product_id
				)
			);
		}

		/**
		 * Display product tab private.
		 *
		 * @version 2.2.0
		 * @since   2.0.0
		 */
		public function display_product_tab_private() {
			$this->display_product_tab( 'private' );
		}

		/**
		 * Display product tab public.
		 *
		 * @version 2.2.0
		 * @since   2.0.0
		 */
		public function display_product_tab_public() {
			$this->display_product_tab( 'public' );
		}
	}

endif;

return new Alg_WC_PN_Display_Frontend();
