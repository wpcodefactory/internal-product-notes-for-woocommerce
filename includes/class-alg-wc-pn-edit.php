<?php
/**
 * Product Notes for WooCommerce - Edit Class
 *
 * @version 3.2.0
 * @since   2.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Product_Notes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PN_Edit' ) ) :

	/**
	 * Alg_WC_PN_Edit class.
	 *
	 * @version 3.2.0
	 * @since   2.0.0
	 */
	class Alg_WC_PN_Edit {

		/**
		 * Is nonce added.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 *
		 * @var bool
		 */
		public $is_nonce_added = false;

		/**
		 * Constructor.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 */
		public function __construct() {
			// Product meta box.
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post_product', array( $this, 'save_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Variations.
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_notes_variation' ), 10, 2 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_notes_variation' ), 10, 2 );
		}

		/**
		 * Add notes variation.
		 *
		 * @version 3.2.0
		 * @since   2.5.0
		 *
		 * @param int   $loop           Loop index.
		 * @param array $variation_data Variation data.
		 *
		 * @todo (dev) `wp_editor`: `quicktags` at least?
		 * @todo (feature) multiple notes per variation?
		 */
		public function add_notes_variation( $loop, $variation_data ) {
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) ) {
					$key     = '_' . alg_wc_pn()->get_id( $private_or_public );
					$label   = (
						'private' === $private_or_public ?
						__( 'Private note', 'product-notes-for-woocommerce' ) :
						__( 'Note', 'product-notes-for-woocommerce' )
					);
					$note_id = 0;
					$value   = '';
					$desc    = '';
					if (
						isset( $variation_data[ $key ][0] ) &&
						is_serialized( $variation_data[ $key ][0] )
					) {
						$data = unserialize( $variation_data[ $key ][0] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
						if ( $data && isset( $data[ $note_id ] ) ) {
							$note = $data[ $note_id ];
							if ( isset( $note['value'] ) ) {
								$value = $note['value'];
							}
							$formatted_time = (
								isset( $note['time'] ) ?
								date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $note['time'] ) :
								''
							);
							$author_name    = ( isset( $note['author'] ) ? get_userdata( $note['author'] ) : '' );
							$author_name    = ( '' !== $author_name ? $author_name->user_login : '' );
							if ( '' !== $formatted_time && '' !== $author_name ) {
								$desc = sprintf(
									/* Translators: %1$s: Author name, %2$s: Formatted time. */
									__( 'Last modified by %1$s on %2$s.', 'product-notes-for-woocommerce' ),
									$author_name,
									$formatted_time
								);
							}
						}
					}
					$id   = "variable{$key}_{$loop}_{$note_id}";
					$name = "variable{$key}[{$loop}][{$note_id}]";
					if ( 'no' === get_option( "alg_wc_pn_{$private_or_public}_wp_editor_variation", 'no' ) ) {
						woocommerce_wp_textarea_input(
							array(
								'id'            => $id,
								'name'          => $name,
								'value'         => $value,
								'label'         => $label,
								'description'   => $desc,
								'desc_tip'      => true,
								'wrapper_class' => 'form-row form-row-full',
								'style'         => get_option( "alg_wc_pn_{$private_or_public}_textarea_style_variation", '' ),
							)
						);
					} else {
						echo '<p class="form-field ' . esc_attr( $id ) . '_field form-row form-row-full">';
						echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
						wp_editor(
							$value,
							$id,
							array(
								'textarea_name' => $name,
								'tinymce'       => false,
							)
						);
						echo '<span class="description">' . wp_kses_post( $desc ) . '</span>';
						echo '</p>';
					}
				}
			}
		}

		/**
		 * Save notes variation.
		 *
		 * @version 2.5.0
		 * @since   2.5.0
		 *
		 * @param int $variation_id Variation ID.
		 * @param int $i            Loop index.
		 */
		public function save_notes_variation( $variation_id, $i ) {
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) ) {
					$key = '_' . alg_wc_pn()->get_id( $private_or_public );
					if ( isset( $_POST[ 'variable' . $key ][ $i ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
						$notes = array_map(
							array( alg_wc_pn()->core, 'sanitize_note' ),
							wp_unslash( $_POST[ 'variable' . $key ][ $i ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						);
						alg_wc_pn()->core->set_product_notes( $notes, $private_or_public, $variation_id );
					}
				}
			}
		}

		/**
		 * Admin enqueue scripts.
		 *
		 * @version 3.2.0
		 * @since   2.0.0
		 *
		 * @param string $hook Current admin page.
		 *
		 * @todo (v3.2.0) `wp_editor`: style is ignored?
		 */
		public function admin_enqueue_scripts( $hook ) {
			if (
				! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ||
				'product' !== get_post_type()
			) {
				return;
			}

			$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ? '' : '.min' );
			wp_enqueue_script(
				'alg-wc-product-notes',
				alg_wc_pn()->plugin_url() . '/assets/js/alg-wc-pn' . $min . '.js',
				array( 'jquery' ),
				alg_wc_pn()->version,
				true
			);

			$private_is_wp_editor = ( 'yes' === get_option( 'alg_wc_pn_private_wp_editor', 'no' ) );
			$public_is_wp_editor  = ( 'yes' === get_option( 'alg_wc_pn_public_wp_editor', 'no' ) );

			wp_localize_script(
				'alg-wc-product-notes',
				'alg_wc_pn',
				array(
					'private_id'           => alg_wc_pn()->get_id( 'private' ),
					'public_id'            => alg_wc_pn()->get_id( 'public' ),
					'delete_text'          => __( 'Delete', 'product-notes-for-woocommerce' ),
					'private_is_wp_editor' => $private_is_wp_editor,
					'public_is_wp_editor'  => $public_is_wp_editor,
				)
			);

			if (
				$private_is_wp_editor ||
				$public_is_wp_editor
			) {
				wp_enqueue_editor();
			}

			// Styles for the admin textarea.
			$style = array();

			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				$id = sanitize_html_class( alg_wc_pn()->get_id( $private_or_public ) );

				$is_wp_editor = ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_wp_editor", 'no' ) );
				$style_value  = (
					! $is_wp_editor ?
					get_option( "alg_wc_pn_{$private_or_public}_textarea_style", 'width:100%;height:150px;' ) :
					'width:100%;'
				);

				$style[] = "textarea.{$id} { {$style_value} }";
			}

			if ( ! empty( $style ) ) {
				wp_register_style(
					'alg-wc-pn-admin-textarea',
					false,
					array(),
					alg_wc_pn()->version
				);

				wp_enqueue_style( 'alg-wc-pn-admin-textarea' );

				wp_add_inline_style(
					'alg-wc-pn-admin-textarea',
					implode( PHP_EOL, $style )
				);
			}
		}

		/**
		 * Add meta boxes.
		 *
		 * @version 2.0.0
		 * @since   1.0.0
		 *
		 * @todo (feature) customizable meta box `context` and `priority`
		 * @todo (feature) customizable meta box visibility (admin and/or shop manager)
		 */
		public function add_meta_boxes() {
			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				add_meta_box(
					"alg-wc-{$private_or_public}-product-notes",
					alg_wc_pn()->core->get_title( $private_or_public, 'plural' ),
					array( $this, "display_{$private_or_public}_notes_meta_box" ),
					'product',
					'side',
					'default'
				);
			}
		}

		/**
		 * Display meta box.
		 *
		 * @version 3.2.0
		 * @since   1.0.0
		 *
		 * @param string $private_or_public Private or public.
		 *
		 * @see https://developer.wordpress.org/reference/functions/wp_editor/
		 * @see https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
		 *
		 * @todo (dev) WPML: `get_the_ID()`
		 * @todo (feature) visual editor: rows
		 * @todo (feature) optionally disable "multiple notes" feature (then save as `string` instead of `array`)?
		 * @todo (dev) restyle; better desc
		 * @todo (dev) nonce
		 */
		public function display_meta_box( $private_or_public ) {
			$id           = alg_wc_pn()->get_id( $private_or_public );
			$html         = '';
			$note_id      = -1;
			$is_wp_editor = ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_wp_editor", 'no' ) );

			foreach ( alg_wc_pn()->core->get_product_notes( $private_or_public ) as $note_id => $note ) {
				$value          = ( isset( $note['value'] ) ? $note['value'] : '' );
				$formatted_time = (
					isset( $note['time'] ) ?
					date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $note['time'] ) :
					''
				);
				$author_name    = ( isset( $note['author'] ) ? get_userdata( $note['author'] ) : '' );
				$author_name    = ( ! empty( $author_name ) ? $author_name->user_login : '' );

				if ( $is_wp_editor ) {
					ob_start();
					wp_editor( $value, $id . '_' . $note_id, array( 'textarea_name' => $id . '[' . $note_id . ']' ) );
					$editor = ob_get_clean();
				} else {
					$editor = '<textarea
						name="' . $id . '[' . $note_id . ']"
						id="' . $id . '_' . $note_id . '"
						class="' . $id . '"
					>' .
						esc_html( $value ) .
					'</textarea>';
				}

				$html .= (
					'<hr>' .
					$editor .
					'<br>' .
					'<input
						type="checkbox"
						name="' . $id . '_del[' . $note_id . ']"
						id="' . $id . '_del_' . $note_id . '"
					>' .
					' ' .
					'<label for="' . $id . '_del_' . $note_id . '">' .
						__( 'Delete', 'product-notes-for-woocommerce' ) .
					'</label>' .
					wc_help_tip( __( 'Check the box and "Update" product to delete.', 'product-notes-for-woocommerce' ) )
				);

				if ( ! empty( $formatted_time ) && ! empty( $author_name ) ) {
					$html .= '<br><em><small>' .
						sprintf(
							/* Translators: %1$s: Author name, %2$s: Formatted time. */
							__( 'last modified by %1$s on %2$s', 'product-notes-for-woocommerce' ),
							$author_name,
							$formatted_time
						) .
					'</small></em>';
				}
			}

			echo (
				'<p>' .
					'<input
						type="button"
						class="button"
						id="' . esc_attr( $id ) . '_add"
						name="' . esc_attr( $id ) . '_add"
						value="' . esc_attr__( 'Add new note', 'product-notes-for-woocommerce' ) . '"
					>' .
				'</p>' .
				'<div class="' . esc_attr( $id ) . '_wrap">' .
					wp_kses(
						$html,
						$this->get_allowed_html()
					) .
					'<input
						type="hidden"
						name="' . esc_attr( $id ) . '_num"
						id="' . esc_attr( $id ) . '_num"
						value="' . esc_attr( $note_id + 1 ) . '"
					>' .
				'</div>'
			);

			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) ) {
				$default_tab_title = (
					'private' === $private_or_public ?
					__( 'Private notes', 'product-notes-for-woocommerce' ) :
					__( 'Notes', 'product-notes-for-woocommerce' )
				);
				$default_tab_title = get_option( "alg_wc_pn_{$private_or_public}_product_tab_title", $default_tab_title );
				$tab_title_id      = $id . '_tab_title';
				$tab_title_value   = get_post_meta( get_the_ID(), $id . '_tab_title', true );

				echo (
					'<hr>' .
					'<label for="' . esc_attr( $tab_title_id ) . '">' .
						'<em>' . esc_html__( 'Tab title:', 'product-notes-for-woocommerce' ) . '</em>' .
					'</label>' .
					'<input
						type="text"
						name="' . esc_attr( $tab_title_id ) . '"
						id="' . esc_attr( $tab_title_id ) . '"
						value="' . esc_attr( $tab_title_value ) . '"
						placeholder="' . esc_attr( $default_tab_title ) . '"
						style="width:100%;"
					>'
				);
			}

			if ( ! $this->is_nonce_added ) {
				wp_nonce_field(
					'alg_wc_pn_save_meta_box',
					'_alg_wc_pn_meta_box_nonce'
				);
				$this->is_nonce_added = true;
			}
		}

		/**
		 * Get allowed HTML.
		 *
		 * @version 3.2.0
		 * @since   3.2.0
		 *
		 * @return array Allowed HTML tags and attributes.
		 */
		public function get_allowed_html() {
			$allowed_html = array(
				'input' => array(
					'type'    => true,
					'id'      => true,
					'name'    => true,
					'class'   => true,
					'style'   => true,
					'value'   => true,
					'checked' => true,
				),
			);

			$allowed_html = array_merge(
				wp_kses_allowed_html( 'post' ),
				$allowed_html
			);

			$allowed_html['button']['aria-pressed']   = true;
			$allowed_html['textarea']['autocomplete'] = true;

			return $allowed_html;
		}

		/**
		 * Display private notes meta box.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		public function display_private_notes_meta_box() {
			$this->display_meta_box( 'private' );
		}

		/**
		 * Display public notes meta box.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 */
		public function display_public_notes_meta_box() {
			$this->display_meta_box( 'public' );
		}

		/**
		 * Save meta boxes.
		 *
		 * @version 3.2.0
		 * @since   1.0.0
		 *
		 * @param int $product_id Product ID.
		 */
		public function save_meta_boxes( $product_id ) {
			if (
				! isset( $_POST['_alg_wc_pn_meta_box_nonce'] ) ||
				! wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_POST['_alg_wc_pn_meta_box_nonce'] ) ),
					'alg_wc_pn_save_meta_box'
				)
			) {
				return;
			}

			foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
				$id = alg_wc_pn()->get_id( $private_or_public );
				if (
					isset( $_POST[ $id ] ) &&
					empty( $_REQUEST['woocommerce_quick_edit'] )
				) {
					$notes = array_map(
						array( alg_wc_pn()->core, 'sanitize_note' ),
						wp_unslash( $_POST[ $id ] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					);
					$del   = (
						isset( $_POST[ $id . '_del' ] ) ?
						array_map(
							'sanitize_textarea_field',
							wp_unslash( $_POST[ $id . '_del' ] )
						) :
						array()
					);
					alg_wc_pn()->core->set_product_notes(
						$notes,
						$private_or_public,
						$product_id,
						$del
					);
				}
				if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) ) {
					$tab_title_id = $id . '_tab_title';
					if (
						isset( $_POST[ $tab_title_id ] ) &&
						empty( $_REQUEST['woocommerce_quick_edit'] )
					) {
						update_post_meta(
							$product_id,
							$tab_title_id,
							sanitize_textarea_field( wp_unslash( $_POST[ $tab_title_id ] ) )
						);
					}
				}
			}
		}
	}

endif;

return new Alg_WC_PN_Edit();
