<?php
/**
 * Product Notes for WooCommerce - Section Settings
 *
 * @version 3.2.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Settings_Section' ) ) :

	/**
	 * Alg_WC_PN_Settings_Section class.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 */
	class Alg_WC_PN_Settings_Section {

		/**
		 * ID.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Description.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var string
		 */
		public $desc;

		/**
		 * Private or public.
		 *
		 * @version 2.9.5
		 * @since   2.9.5
		 *
		 * @var string
		 */
		public $private_or_public;

		/**
		 * Constructor.
		 *
		 * @version 2.0.0
		 * @since   1.0.0
		 *
		 * @param string $id                Section ID.
		 * @param string $desc              Section description.
		 * @param string $private_or_public Private or public.
		 *
		 * @todo (dev) code refactoring (i.e., `Alg_WC_PN_Settings_General`, separate functions, etc.)
		 */
		public function __construct( $id, $desc, $private_or_public ) {
			$this->id                = $id;
			$this->desc              = $desc;
			$this->private_or_public = $private_or_public;

			add_filter(
				'woocommerce_get_sections_alg_wc_product_notes',
				array( $this, 'settings_section' )
			);
			add_filter(
				'woocommerce_get_settings_alg_wc_product_notes_' . $this->id,
				array( $this, 'get_settings' ),
				PHP_INT_MAX
			);
		}

		/**
		 * Settings section.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $sections Sections array.
		 */
		public function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

		/**
		 * Get single or loop sections.
		 *
		 * @version 2.7.0
		 * @since   2.7.0
		 */
		public function get_single_or_loop_sections() {
			return array(
				'single' => __( 'Single product page', 'product-notes-for-woocommerce' ),
				'loop'   => __( 'Shop pages', 'product-notes-for-woocommerce' ),
			);
		}

		/**
		 * Get single or loop hooks.
		 *
		 * @version 2.7.0
		 * @since   2.7.0
		 */
		public function get_single_or_loop_hooks() {
			return array(
				'single' => array(
					'woocommerce_before_single_product'  => __( 'Before product', 'product-notes-for-woocommerce' ),
					'woocommerce_before_single_product_summary' => __( 'Before product summary', 'product-notes-for-woocommerce' ),
					'woocommerce_single_product_summary' => __( 'In product summary', 'product-notes-for-woocommerce' ),
					'woocommerce_after_single_product_summary' => __( 'After product summary', 'product-notes-for-woocommerce' ),
					'woocommerce_after_single_product'   => __( 'After product', 'product-notes-for-woocommerce' ),
				),
				'loop'   => array(
					'woocommerce_before_shop_loop_item' => __( 'Before product', 'product-notes-for-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'product-notes-for-woocommerce' ),
					'woocommerce_shop_loop_item_title'  => __( 'In product title', 'product-notes-for-woocommerce' ),
					'woocommerce_after_shop_loop_item_title' => __( 'After product title', 'product-notes-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'  => __( 'After product', 'product-notes-for-woocommerce' ),
				),
			);
		}

		/**
		 * Get single or loop hooked info.
		 *
		 * @version 2.7.0
		 * @since   2.7.0
		 */
		public function get_single_or_loop_hooked_info() {
			return array(
				'single' => array(
					'woocommerce_before_single_product'  => array(
						/* translators: %d: priority */
						sprintf( __( 'Notices: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
					'woocommerce_before_single_product_summary' => array(
						/* translators: %d: priority */
						sprintf( __( 'Sale flash: %d', 'product-notes-for-woocommerce' ), 10 ),
						/* translators: %d: priority */
						sprintf( __( 'Images: %d', 'product-notes-for-woocommerce' ), 20 ),
					),
					'woocommerce_single_product_summary' => array(
						/* translators: %d: priority */
						sprintf( __( 'Title: %d', 'product-notes-for-woocommerce' ), 5 ),
						/* translators: %d: priority */
						sprintf( __( 'Rating: %d', 'product-notes-for-woocommerce' ), 10 ),
						/* translators: %d: priority */
						sprintf( __( 'Price: %d', 'product-notes-for-woocommerce' ), 10 ),
						/* translators: %d: priority */
						sprintf( __( 'Excerpt: %d', 'product-notes-for-woocommerce' ), 20 ),
						/* translators: %d: priority */
						sprintf( __( 'Add to cart: %d', 'product-notes-for-woocommerce' ), 30 ),
						/* translators: %d: priority */
						sprintf( __( 'Meta: %d', 'product-notes-for-woocommerce' ), 40 ),
						/* translators: %d: priority */
						sprintf( __( 'Sharing: %d', 'product-notes-for-woocommerce' ), 50 ),
					),
					'woocommerce_after_single_product_summary' => array(
						/* translators: %d: priority */
						sprintf( __( 'Tabs: %d', 'product-notes-for-woocommerce' ), 10 ),
						/* translators: %d: priority */
						sprintf( __( 'Upsells: %d', 'product-notes-for-woocommerce' ), 15 ),
						/* translators: %d: priority */
						sprintf( __( 'Related products: %d', 'product-notes-for-woocommerce' ), 20 ),
					),
				),
				'loop'   => array(
					'woocommerce_before_shop_loop_item' => array(
						/* translators: %d: priority */
						sprintf( __( 'Link open: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
					'woocommerce_before_shop_loop_item_title' => array(
						/* translators: %d: priority */
						sprintf( __( 'Sale flash: %d', 'product-notes-for-woocommerce' ), 10 ),
						/* translators: %d: priority */
						sprintf( __( 'Thumbnail: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
					'woocommerce_shop_loop_item_title'  => array(
						/* translators: %d: priority */
						sprintf( __( 'Title: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
					'woocommerce_after_shop_loop_item_title' => array(
						/* translators: %d: priority */
						sprintf( __( 'Rating: %d', 'product-notes-for-woocommerce' ), 5 ),
						/* translators: %d: priority */
						sprintf( __( 'Price: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
					'woocommerce_after_shop_loop_item'  => array(
						/* translators: %d: priority */
						sprintf( __( 'Link close: %d', 'product-notes-for-woocommerce' ), 5 ),
						/* translators: %d: priority */
						sprintf( __( 'Add to cart: %d', 'product-notes-for-woocommerce' ), 10 ),
					),
				),
			);
		}

		/**
		 * Get settings.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 *
		 * @todo (desc) `_order_item_meta`: "Same notes will be displayed even if the product will be removed, or notes updated."?
		 * @todo (dev) Frontend Options: Single/Loop: add more hooks?
		 * @todo (dev) Frontend Options: Single/Loop: different defaults (content, priority, etc.) per hook?
		 * @todo (dev) WooCommerce PDF Invoices & Packing Slips: better desc, and maybe move to a new subsection, e.g., "Compatibility Options"
		 * @todo (dev) store all settings in arrays by group, i.e., `alg_wc_pn_{$private_or_public}_product_tab_options[enabled]`, etc.
		 * @todo (dev) Product meta: add "Position priority" option?
		 */
		public function get_settings() {

			$private_or_public = $this->private_or_public;

			switch ( $private_or_public ) {
				case 'private':
					$section_title        = __( 'Private Notes Options', 'product-notes-for-woocommerce' );
					$option_title         = __( 'Private notes', 'product-notes-for-woocommerce' );
					$frontend_desc        = '<strong>' . __( 'Private notes on frontend will be visible to admin and shop manager only.', 'product-notes-for-woocommerce' ) . '</strong>';
					$default_tab_title    = __( 'Private notes', 'product-notes-for-woocommerce' );
					$default_tab_priority = 100;
					$default_dokan_title  = __( 'Private Product Note', 'product-notes-for-woocommerce' );
					break;
				case 'public':
					$section_title        = __( 'Public Notes Options', 'product-notes-for-woocommerce' );
					$option_title         = __( 'Public notes', 'product-notes-for-woocommerce' );
					$frontend_desc        = '';
					$default_tab_title    = __( 'Notes', 'product-notes-for-woocommerce' );
					$default_tab_priority = 101;
					$default_dokan_title  = __( 'Public Product Note', 'product-notes-for-woocommerce' );
					break;
			}

			$all_hooks   = $this->get_single_or_loop_hooks();
			$hooked_info = $this->get_single_or_loop_hooked_info();

			$general_settings = array(
				array(
					'title' => $section_title,
					'type'  => 'title',
					'id'    => "alg_wc_pn_{$private_or_public}_section_options",
				),
				array(
					'title'   => $option_title,
					'desc'    => '<strong>' . __( 'Enable section', 'product-notes-for-woocommerce' ) . '</strong>',
					'id'      => "alg_wc_pn_{$private_or_public}_enabled",
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => "alg_wc_pn_{$private_or_public}_section_options",
				),
				array(
					'title' => __( 'General Options', 'product-notes-for-woocommerce' ),
					'type'  => 'title',
					'id'    => "alg_wc_pn_{$private_or_public}_general_options",
				),
				array(
					'title'    => __( 'Note per variation', 'product-notes-for-woocommerce' ),
					'desc_tip' => __( 'Check this if you want to add notes to each variation separately.', 'product-notes-for-woocommerce' ) . '<br>' .
						__( 'Note fields will be added to the "Variations" tab on admin product edit page, right after the standard "Description" fields.', 'product-notes-for-woocommerce' ),
					'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
					'id'       => "alg_wc_pn_{$private_or_public}_variations",
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => "alg_wc_pn_{$private_or_public}_general_options",
				),
			);

			$frontend_settings = array(
				array(
					'title' => __( 'Frontend Options', 'product-notes-for-woocommerce' ),
					'desc'  => $frontend_desc,
					'type'  => 'title',
					'id'    => "alg_wc_pn_{$private_or_public}_frontend_options",
				),
				array(
					'title'    => __( 'Product tab', 'product-notes-for-woocommerce' ),
					'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
					'desc_tip' => __( 'Show product notes in tab on frontend.', 'product-notes-for-woocommerce' ),
					'id'       => "alg_wc_pn_{$private_or_public}_product_tab",
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'desc'    => __( 'Tab title', 'product-notes-for-woocommerce' ),
					'id'      => "alg_wc_pn_{$private_or_public}_product_tab_title",
					'default' => $default_tab_title,
					'type'    => 'text',
				),
				array(
					'desc'    => __( 'Tab title per product', 'product-notes-for-woocommerce' ),
					'id'      => "alg_wc_pn_{$private_or_public}_product_tab_title_per_product",
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'desc'    => __( 'Tab priority (i.e., position)', 'product-notes-for-woocommerce' ),
					'id'      => "alg_wc_pn_{$private_or_public}_product_tab_priority",
					'default' => $default_tab_priority,
					'type'    => 'number',
				),
				array(
					'title'    => __( 'Product meta', 'product-notes-for-woocommerce' ),
					'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
					'desc_tip' => __( 'Show product notes in product meta on frontend.', 'product-notes-for-woocommerce' ),
					'id'       => "alg_wc_pn_{$private_or_public}_product_meta",
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'desc'    => __( 'Position', 'product-notes-for-woocommerce' ),
					'id'      => "alg_wc_pn_{$private_or_public}_product_meta_position",
					'default' => 'woocommerce_product_meta_end',
					'type'    => 'select',
					'class'   => 'chosen_select',
					'options' => array(
						'woocommerce_product_meta_start' => __( 'Before product meta', 'product-notes-for-woocommerce' ),
						'woocommerce_product_meta_end'   => __( 'After product meta', 'product-notes-for-woocommerce' ),
					),
				),
			);
			foreach ( $this->get_single_or_loop_sections() as $single_or_loop => $section_title ) {
				$frontend_settings = array_merge(
					$frontend_settings,
					array(
						array(
							'title'    => $section_title,
							'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
							'desc_tip' => sprintf(
								/* translators: %s: section title */
								__( 'Show product notes on frontend: %s.', 'product-notes-for-woocommerce' ),
								$section_title
							),
							'id'       => "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_enabled",
							'default'  => 'no',
							'type'     => 'checkbox',
						),
						array(
							'desc'     => __( 'Position(s)', 'product-notes-for-woocommerce' ),
							'desc_tip' => __( 'New option fields will be displayed after you "Save changes".', 'product-notes-for-woocommerce' ),
							'id'       => "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_position",
							'default'  => array(),
							'type'     => 'multiselect',
							'class'    => 'chosen_select',
							'options'  => $all_hooks[ $single_or_loop ],
						),
					)
				);

				$hooks = get_option( "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_position", array() );
				foreach ( $hooks as $hook ) {
					$frontend_settings = array_merge(
						$frontend_settings,
						array(
							array(
								'desc'    => (
									'<em>' .
										sprintf(
											/* translators: %s: hook name */
											__( 'Position: %s', 'product-notes-for-woocommerce' ),
											( $all_hooks[ $single_or_loop ][ $hook ] ?? $hook )
										) .
									'</em>' .
									'<br>' .
									sprintf(
										/* translators: %s: placeholder */
										__( 'Placeholder: %s', 'product-notes-for-woocommerce' ),
										'<code>%product_notes%</code>'
									)
								),
								'id'      => "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_content[{$hook}]",
								'default' => '<p>%product_notes%</p>',
								'type'    => 'textarea',
							),
							array(
								'desc'     => __( 'Priority (i.e., order inside the "Position")', 'product-notes-for-woocommerce' ),
								'desc_tip' => (
									sprintf(
										/* translators: %s: hook name */
										__( 'Position: %s', 'product-notes-for-woocommerce' ),
										( $all_hooks[ $single_or_loop ][ $hook ] ?? $hook )
									) .
									'<br><br>' .
									(
										isset( $hooked_info[ $single_or_loop ][ $hook ] ) ?
										sprintf(
											/* translators: %s: list of known priorities for the hook */
											__( 'Known priorities: %s', 'product-notes-for-woocommerce' ),
											'<br>' . implode( '<br>', $hooked_info[ $single_or_loop ][ $hook ] )
										) :
										''
									)
								),
								'id'       => "alg_wc_pn_{$private_or_public}_{$single_or_loop}_frontend_priority[{$hook}]",
								'default'  => 10,
								'type'     => 'number',
							),
						)
					);
				}
			}
			$frontend_settings = array_merge(
				$frontend_settings,
				array(
					array(
						'title'    => __( 'Variation description', 'product-notes-for-woocommerce' ),
						'desc_tip' => (
							__( 'Show product notes in variation descriptions on frontend.', 'product-notes-for-woocommerce' ) .
							' ' .
							sprintf(
								/* translators: %s: option name */
								__( 'Please note that "%s" option must be enabled.', 'product-notes-for-woocommerce' ),
								__( 'Note per variation', 'product-notes-for-woocommerce' )
							)
						),
						'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
						'id'       => "alg_wc_pn_{$private_or_public}_variation_description",
						'type'     => 'checkbox',
						'default'  => 'no',
					),
					array(
						'title'    => __( 'Cart', 'product-notes-for-woocommerce' ),
						'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip' => __( 'Show product notes in cart on frontend.', 'product-notes-for-woocommerce' ),
						'id'       => "alg_wc_pn_{$private_or_public}_cart",
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'title'    => __( 'Checkout', 'product-notes-for-woocommerce' ),
						'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip' => __( 'Show product notes in checkout on frontend.', 'product-notes-for-woocommerce' ),
						'id'       => "alg_wc_pn_{$private_or_public}_checkout",
						'default'  => 'no',
						'type'     => 'checkbox',
					),
				)
			);
			if ( 'public' === $private_or_public ) {
				$frontend_settings = array_merge(
					$frontend_settings,
					array(
						array(
							'title'    => __( 'Logged users only', 'product-notes-for-woocommerce' ),
							'desc'     => __( 'Enable', 'product-notes-for-woocommerce' ),
							'desc_tip' => __( 'Show public product notes to logged in users only.', 'product-notes-for-woocommerce' ),
							'id'       => 'alg_wc_pn_public_logged_in_user_only',
							'default'  => 'no',
							'type'     => 'checkbox',
						),
					)
				);
			}
			$frontend_settings = array_merge(
				$frontend_settings,
				array(
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_pn_{$private_or_public}_frontend_options",
					),
				)
			);

			$backend_settings = array(
				array(
					'title' => __( 'Backend Options', 'product-notes-for-woocommerce' ),
					'desc'  => apply_filters(
						'alg_wc_pn_settings',
						sprintf(
							'<p style="background-color:white;padding:10px;"><strong>' .
								"You'll need %s plugin version for the disabled options." .
							'</strong></p>',
							'<a href="https://wpfactory.com/item/product-notes-for-woocommerce/" target="_blank">' .
								'Product Notes for WooCommerce Pro' .
							'</a>'
						)
					),
					'type'  => 'title',
					'id'    => "alg_wc_pn_{$private_or_public}_backend_options",
				),
				array(
					'title'         => __( 'Visual editor', 'product-notes-for-woocommerce' ),
					'desc_tip'      => __( 'Use visual editor in product notes.', 'product-notes-for-woocommerce' ),
					'desc'          => __( 'Product notes', 'product-notes-for-woocommerce' ),
					'id'            => "alg_wc_pn_{$private_or_public}_wp_editor",
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),
				array(
					'desc_tip'      => __( 'Use <em>simplified</em> visual editor in variation notes.', 'product-notes-for-woocommerce' ),
					'desc'          => __( 'Variation notes', 'product-notes-for-woocommerce' ),
					'id'            => "alg_wc_pn_{$private_or_public}_wp_editor_variation",
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end',
				),
				array(
					'title'    => __( 'Textarea style', 'product-notes-for-woocommerce' ),
					'desc'     => __( 'Product notes', 'product-notes-for-woocommerce' ),
					'desc_tip' => __( "Styling for the textarea input on product's admin edit page.", 'product-notes-for-woocommerce' ) . ' ' .
					sprintf(
						/* translators: %1$s: option name, %2$s: option name */
						__( 'Ignored if "%1$s > %2$s" option is enabled.', 'product-notes-for-woocommerce' ),
						__( 'Visual editor', 'product-notes-for-woocommerce' ),
						__( 'Product notes', 'product-notes-for-woocommerce' )
					),
					'id'       => "alg_wc_pn_{$private_or_public}_textarea_style",
					'default'  => 'width:100%;height:150px;',
					'type'     => 'text',
				),
				array(
					'desc'     => __( 'Variation notes', 'product-notes-for-woocommerce' ),
					'desc_tip' => __( "Styling for the textarea input on variation's admin edit page.", 'product-notes-for-woocommerce' ) . ' ' .
					sprintf(
						/* translators: %1$s: option name, %2$s: option name */
						__( 'Ignored if "%1$s > %2$s" option is enabled.', 'product-notes-for-woocommerce' ),
						__( 'Visual editor', 'product-notes-for-woocommerce' ),
						__( 'Variation notes', 'product-notes-for-woocommerce' )
					),
					'id'       => "alg_wc_pn_{$private_or_public}_textarea_style_variation",
					'default'  => '',
					'type'     => 'text',
				),
				array(
					'title'             => __( 'Admin products list column', 'product-notes-for-woocommerce' ),
					'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
					'desc_tip'          => __( 'Show product notes in admin products list column.', 'product-notes-for-woocommerce' ),
					'id'                => "alg_wc_pn_{$private_or_public}_products_column",
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Admin orders', 'product-notes-for-woocommerce' ),
					'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
					'desc_tip'          => __( 'Show product notes in admin orders.', 'product-notes-for-woocommerce' ),
					'id'                => "alg_wc_pn_{$private_or_public}_admin_orders",
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => __( 'Save in order items meta', 'product-notes-for-woocommerce' ),
					'desc_tip'          => (
						__( 'Will save notes in new order items.', 'product-notes-for-woocommerce' ) .
						' ' .
						__( 'And you will be able to edit notes on admin order page.', 'product-notes-for-woocommerce' ) .
						'<br>' .
						sprintf(
							/* translators: %s: item meta key */
							__( 'Item meta key: %s', 'product-notes-for-woocommerce' ),
							'<code>' .
								'_' . alg_wc_pn()->get_id( $private_or_public ) .
							'</code>'
						)
					),
					'id'                => "alg_wc_pn_{$private_or_public}_order_item_meta",
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'title'             => __( 'Admin emails', 'product-notes-for-woocommerce' ),
					'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
					'desc_tip'          => __( 'Show product notes in admin order emails.', 'product-notes-for-woocommerce' ),
					'id'                => "alg_wc_pn_{$private_or_public}_admin_emails",
					'default'           => 'no',
					'type'              => 'checkbox',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
			);
			if ( 'public' === $private_or_public ) {
				$backend_settings = array_merge(
					$backend_settings,
					array(
						array(
							'title'             => __( 'Customer emails', 'product-notes-for-woocommerce' ),
							'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
							'desc_tip'          => __( 'Show product notes in customer order emails.', 'product-notes-for-woocommerce' ),
							'id'                => 'alg_wc_pn_public_customer_emails',
							'default'           => 'no',
							'type'              => 'checkbox',
							'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
						),
					)
				);
			}
			$backend_settings = array_merge(
				$backend_settings,
				array(
					array(
						'title'             => __( 'Admin search', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'Make product notes searchable by admin.', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_admin_search",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => __( 'JSON Search', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'This will also make products notes searchable in admin JSON, e.g., when searching for "Linked Products".', 'product-notes-for-woocommerce' ) . ' ' .
							__( '"Admin search" option must be enabled.', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_admin_search_json",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Export', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'Make product notes exportable in "Products > Export".', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_export",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Import', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'Make product notes importable in "Products > Import".', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_import",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Quick edit', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'Make product notes editable in "Quick Edit".', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_quick_edit",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Bulk edit', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => __( 'Make product notes editable in "Bulk Actions > Edit".', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_bulk_edit",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_pn_{$private_or_public}_backend_options",
					),
					array(
						'title' => __( 'Compatibility Options', 'product-notes-for-woocommerce' ),
						'desc'  => apply_filters(
							'alg_wc_pn_settings',
							sprintf(
								'<p style="background-color:white;padding:10px;"><strong>' .
									"You'll need %s plugin version to use this section." .
								'</strong></p>',
								'<a href="https://wpfactory.com/item/product-notes-for-woocommerce/" target="_blank">' .
									'Product Notes for WooCommerce Pro' .
								'</a>'
							)
						),
						'type'  => 'title',
						'id'    => "alg_wc_pn_{$private_or_public}_compatibility_options",
					),
					array(
						'title'             => __( 'WooCommerce PDF Invoices & Packing Slips', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => sprintf(
							/* translators: %s: plugin link */
							__( 'Show product notes in PDF documents from the %s plugin.', 'product-notes-for-woocommerce' ),
							'<a target="_blank" href="https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/">' .
								__( 'WooCommerce PDF Invoices & Packing Slips', 'product-notes-for-woocommerce' ) .
							'</a>'
						),
						'id'                => "alg_wc_pn_{$private_or_public}_wpo_wcpdf",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => (
							sprintf(
								/* translators: %s: example template types */
								__( 'Comma separated list of PDF document template types to show product notes in, e.g., %s.', 'product-notes-for-woocommerce' ),
								'<code>invoice,packing-slip</code>'
							) .
							' ' .
							__( 'Leave empty to show notes in all template types.', 'product-notes-for-woocommerce' )
						),
						'id'                => "alg_wc_pn_{$private_or_public}_wpo_wcpdf_template_type",
						'default'           => '',
						'type'              => 'text',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Product/Review CSV Import Export', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => sprintf(
							/* translators: 1: plugin link, 2: plugin author */
							__( 'Make product notes importable in the %1$s plugin (by %2$s).', 'product-notes-for-woocommerce' ),
							'<a href="https://www.webtoffee.com/product/product-import-export-woocommerce/" target="_blank">' .
								__( 'Product/Review CSV Import Export', 'product-notes-for-woocommerce' ) .
							'</a>',
							'WebToffee'
						),
						'id'                => "alg_wc_pn_{$private_or_public}_webtoffee_import",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'title'             => __( 'Dokan', 'product-notes-for-woocommerce' ),
						'desc'              => __( 'Enable', 'product-notes-for-woocommerce' ),
						'desc_tip'          => sprintf(
							/* translators: 1: plugin link */
							__( 'Show product notes in vendor product form from the %s plugin.', 'product-notes-for-woocommerce' ),
							'<a target="_blank" href="https://wordpress.org/plugins/dokan-lite/">' .
								__( 'Dokan', 'product-notes-for-woocommerce' ) .
							'</a>'
						),
						'id'                => "alg_wc_pn_{$private_or_public}_dokan",
						'default'           => 'no',
						'type'              => 'checkbox',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => __( 'Title', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_dokan_options[title]",
						'default'           => $default_dokan_title,
						'type'              => 'text',
						'css'               => 'width:100%;',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => __( 'Textarea placeholder', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_dokan_options[textarea_placeholder]",
						'default'           => __( 'Enter some product notes...', 'product-notes-for-woocommerce' ),
						'type'              => 'text',
						'css'               => 'width:100%;',
						'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
					),
					array(
						'desc'              => __( 'Textarea rows', 'product-notes-for-woocommerce' ),
						'id'                => "alg_wc_pn_{$private_or_public}_dokan_options[textarea_rows]",
						'default'           => 3,
						'type'              => 'number',
						'custom_attributes' => array_merge( array( 'min' => 1 ), apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ), 'array' ) ),
					),
					array(
						'type' => 'sectionend',
						'id'   => "alg_wc_pn_{$private_or_public}_compatibility_options",
					),
				)
			);

			$tools = array(
				array(
					'title' => __( 'Tools', 'product-notes-for-woocommerce' ),
					'desc'  => __( 'Check the box and save changes to run the tool.', 'product-notes-for-woocommerce' ) . ' ' .
					__( 'Please note that there is no undo for these tools.', 'product-notes-for-woocommerce' ) .
					apply_filters(
						'alg_wc_pn_settings',
						sprintf(
							'<p style="background-color:white;padding:10px;"><strong>' .
								"You'll need %s plugin version for the disabled options." .
							'</strong></p>',
							'<a href="https://wpfactory.com/item/product-notes-for-woocommerce/" target="_blank">' .
								'Product Notes for WooCommerce Pro' .
							'</a>'
						)
					),
					'type'  => 'title',
					'id'    => "alg_wc_pn_{$private_or_public}_tools",
				),
				array(
					'title'    => __( 'Products', 'product-notes-for-woocommerce' ),
					'desc'     => '<strong>' . __( 'Delete', 'product-notes-for-woocommerce' ) . '</strong>',
					'desc_tip' => __( 'Deletes all notes for all products.', 'product-notes-for-woocommerce' ),
					'id'       => "alg_wc_pn_{$private_or_public}_tool_delete_all",
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'             => __( 'Orders', 'product-notes-for-woocommerce' ),
					'desc'              => '<strong>' . __( 'Add', 'product-notes-for-woocommerce' ) . '</strong>',
					'desc_tip'          => __( 'Adds product notes to all orders items meta.', 'product-notes-for-woocommerce' ),
					'id'                => "alg_wc_pn_{$private_or_public}_tool_add_order",
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'start',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'desc'              => '<strong>' . __( 'Delete', 'product-notes-for-woocommerce' ) . '</strong>',
					'desc_tip'          => __( 'Deletes all products notes from all orders items meta.', 'product-notes-for-woocommerce' ),
					'id'                => "alg_wc_pn_{$private_or_public}_tool_delete_all_order",
					'default'           => 'no',
					'type'              => 'checkbox',
					'checkboxgroup'     => 'end',
					'custom_attributes' => apply_filters( 'alg_wc_pn_settings', array( 'disabled' => 'disabled' ) ),
				),
				array(
					'type' => 'sectionend',
					'id'   => "alg_wc_pn_{$private_or_public}_tools",
				),
			);

			return array_merge( $general_settings, $frontend_settings, $backend_settings, $tools );
		}
	}

endif;
