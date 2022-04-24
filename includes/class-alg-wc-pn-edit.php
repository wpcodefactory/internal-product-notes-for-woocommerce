<?php
/**
 * Product Notes for WooCommerce - Edit Class
 *
 * @version 2.7.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Product_Notes_Edit' ) ) :

class Alg_WC_Product_Notes_Edit {

	/**
	 * Constructor.
	 *
	 * @version 2.5.0
	 * @since   2.0.0
	 */
	function __construct() {
		// Product meta box
		add_action( 'add_meta_boxes',        array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_product',     array( $this, 'save_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		// Variations
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_notes_variation' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation',            array( $this, 'save_notes_variation' ), 10, 2 );
	}

	/**
	 * add_notes_variation.
	 *
	 * @version 2.7.0
	 * @since   2.5.0
	 *
	 * @todo    [next] (dev) `wp_editor`: `quicktags` at least?
	 * @todo    [later] (feature) multiple notes per variation?
	 */
	function add_notes_variation( $loop, $variation_data, $variation ) {
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) ) {
				$key     = '_' . alg_wc_pn()->get_id( $private_or_public );
				$label   = ( 'private' === $private_or_public ? __( 'Private note', 'product-notes-for-woocommerce' ) : __( 'Note', 'product-notes-for-woocommerce' ) );
				$note_id = 0;
				$value   = '';
				$desc    = '';
				if ( isset( $variation_data[ $key ][0] ) && is_serialized( $variation_data[ $key ][0] ) && ( $data = unserialize( $variation_data[ $key ][0] ) ) ) {
					if ( isset( $data[ $note_id ] ) ) {
						$note = $data[ $note_id ];
						if ( isset( $note['value'] ) ) {
							$value = $note['value'];
						}
						$formatted_time = ( isset( $note['time'] )   ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $note['time'] ) : '' );
						$author_name    = ( isset( $note['author'] ) ? get_userdata( $note['author'] ) : '' );
						$author_name    = ( '' != $author_name ? $author_name->user_login : '' );
						if ( '' != $formatted_time && '' != $author_name ) {
							$desc = sprintf( __( 'Last modified by %s on %s.', 'product-notes-for-woocommerce' ), $author_name, $formatted_time );
						}
					}
				}
				$id   = "variable{$key}_{$loop}_{$note_id}";
				$name = "variable{$key}[{$loop}][{$note_id}]";
				if ( 'no' === get_option( "alg_wc_pn_{$private_or_public}_wp_editor_variation", 'no' ) ) {
					woocommerce_wp_textarea_input( array(
						'id'                => $id,
						'name'              => $name,
						'value'             => $value,
						'label'             => $label,
						'description'       => $desc,
						'desc_tip'          => true,
						'wrapper_class'     => 'form-row form-row-full',
						'style'             => get_option( "alg_wc_pn_{$private_or_public}_textarea_style_variation", '' ),
					) );
				} else {
					echo '<p class="form-field ' . $id . '_field form-row form-row-full">' . '<label for="' . $id . '">' . $label . '</label>';
					wp_editor( $value, $id, array( 'textarea_name' => $name, 'tinymce' => false ) );
					echo '<span class="description">' . $desc . '</span>' . '</p>';
				}
			}
		}
	}

	/**
	 * save_notes_variation.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 */
	function save_notes_variation( $variation_id, $i ) {
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_variations", 'no' ) ) {
				$key = '_' . alg_wc_pn()->get_id( $private_or_public );
				if ( isset( $_POST[ 'variable' . $key ][ $i ] ) ) {
					$notes = array_map( array( alg_wc_pn()->core, 'sanitize_note' ), $_POST[ 'variable' . $key ][ $i ] );
					alg_wc_pn()->core->set_product_notes( $notes, $private_or_public, $variation_id );
				}
			}
		}
	}

	/**
	 * admin_enqueue_scripts.
	 *
	 * @version 2.5.1
	 * @since   2.0.0
	 */
	function admin_enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}
		wp_enqueue_script( 'alg_wc_product_notes',
			alg_wc_pn()->plugin_url() . '/includes/js/alg-wc-pn' . ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' ) . '.js',
			array( 'jquery' ),
			alg_wc_pn()->version,
			true
		);
		$private_is_wp_editor = ( 'yes' === get_option( 'alg_wc_pn_private_wp_editor', 'no' ) );
		$public_is_wp_editor  = ( 'yes' === get_option( 'alg_wc_pn_public_wp_editor', 'no' ) );
		wp_localize_script( 'alg_wc_product_notes',
			'alg_wc_pn',
			array(
				'private_id'           => alg_wc_pn()->get_id( 'private' ),
				'public_id'            => alg_wc_pn()->get_id( 'public' ),
				'delete_text'          => __( 'Delete', 'product-notes-for-woocommerce' ),
				'private_is_wp_editor' => $private_is_wp_editor,
				'public_is_wp_editor'  => $public_is_wp_editor,
			)
		);
		if ( $private_is_wp_editor || $public_is_wp_editor ) {
			global $pagenow;
			if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) && 'product' === get_post_type() ) {
				wp_enqueue_editor();
			}
		}
	}

	/**
	 * add_meta_boxes.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (feature) customizable meta box `context` and `priority`
	 * @todo    [maybe] (feature) customizable meta box visibility (admin and/or shop manager)
	 */
	function add_meta_boxes() {
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			add_meta_box( "alg-wc-{$private_or_public}-product-notes",
				alg_wc_pn()->core->get_title( $private_or_public, 'plural' ),
				array( $this, "display_{$private_or_public}_notes_meta_box" ),
				'product',
				'side',
				'default'
			);
		}
	}

	/**
	 * display_meta_box.
	 *
	 * @version 2.5.1
	 * @since   1.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/wp_editor/
	 * @see     https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
	 *
	 * @todo    [next] (dev) WPML: `get_the_ID()`
	 * @todo    [next] (feature) visual editor: rows
	 * @todo    [maybe] (feature) optionally disable "multiple notes" feature (then save as `string` instead of `array`)?
	 * @todo    [maybe] (dev) restyle; better desc
	 * @todo    [maybe] (dev) nonce
	 */
	function display_meta_box( $private_or_public ) {
		$id           = alg_wc_pn()->get_id( $private_or_public );
		$html         = '';
		$note_id      = -1;
		$is_wp_editor = ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_wp_editor", 'no' ) );
		foreach ( alg_wc_pn()->core->get_product_notes( $private_or_public ) as $note_id => $note ) {
			$value          = ( isset( $note['value'] )  ? $note['value'] : '' );
			$formatted_time = ( isset( $note['time'] )   ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $note['time'] ) : '' );
			$author_name    = ( isset( $note['author'] ) ? get_userdata( $note['author'] ) : '' );
			$author_name    = ( '' != $author_name ? $author_name->user_login : '' );
			if ( $is_wp_editor ) {
				ob_start();
				wp_editor( $value, $id . '_' . $note_id, array( 'textarea_name' => $id . '[' . $note_id . ']' ) );
				$editor = ob_get_clean();
			} else {
				$editor = '<textarea name="' . $id . '[' . $note_id . ']" id="' . $id . '_' . $note_id . '"' . ' class="' . $id . '">' . esc_html( $value ) . '</textarea>';
			}
			$html .= '<hr>' .
				$editor . '<br>' .
				'<input type="checkbox" name="' . $id . '_del[' . $note_id . ']" id="' . $id . '_del_' . $note_id . '">' . ' ' .
					'<label for="' . $id . '_del_' . $note_id . '">' . __( 'Delete', 'product-notes-for-woocommerce' ) . '</label>' .
					wc_help_tip( __( 'Check the box and "Update" product to delete.', 'product-notes-for-woocommerce' ) );
			if ( '' != $formatted_time && '' != $author_name ) {
				$html .= '<br><em><small>' . sprintf( __( 'last modified by %s on %s', 'product-notes-for-woocommerce' ), $author_name, $formatted_time ) . '</small></em>';
			}
		}
		if ( ! $is_wp_editor ) {
			echo '<style>textarea.' . $id . ' { ' . get_option( "alg_wc_pn_{$private_or_public}_textarea_style", 'width:100%;height:150px;' ) . ' }</style>';
		} else {
			echo '<style>textarea.' . $id . ' { width:100%; }</style>';
		}
		echo '<p><input type="button" class="button" id="' . $id . '_add" name="' . $id . '_add" value="' .
				__( 'Add new note', 'product-notes-for-woocommerce' ) . '"></p>' .
			'<div class="' . $id . '_wrap">' .
				$html .
				'<input type="hidden" name="' . $id . '_num" id="' . $id . '_num" value="' . ( $note_id + 1 ) . '">' .
			'</div>';
		if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) ) {
			$default_tab_title = ( 'private' === $private_or_public ? __( 'Private notes', 'product-notes-for-woocommerce' ) : __( 'Notes', 'product-notes-for-woocommerce' ) );
			$default_tab_title = get_option( "alg_wc_pn_{$private_or_public}_product_tab_title", $default_tab_title );
			$tab_title_id      = $id . '_tab_title';
			$tab_title_value   = get_post_meta( get_the_ID(), $id . '_tab_title', true );
			echo '<hr>' .
				'<label for="' . $tab_title_id . '">' . '<em>' . __( 'Tab title:', 'product-notes-for-woocommerce' ) . '</em>' . '</label>' .
				'<input type="text" name="' . $tab_title_id . '" id="' . $tab_title_id . '" class="" value="' . esc_html( $tab_title_value ) . '" placeholder="' . $default_tab_title . '" style="width:100%;">';
		}
	}

	/**
	 * display_private_notes_meta_box.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function display_private_notes_meta_box() {
		$this->display_meta_box( 'private' );
	}

	/**
	 * display_public_notes_meta_box.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function display_public_notes_meta_box() {
		$this->display_meta_box( 'public' );
	}

	/**
	 * save_meta_boxes.
	 *
	 * @version 2.3.1
	 * @since   1.0.0
	 */
	function save_meta_boxes( $product_id ) {
		foreach ( alg_wc_pn_get_enabled_sections() as $private_or_public ) {
			$id = alg_wc_pn()->get_id( $private_or_public );
			if ( isset( $_POST[ $id ] ) && empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
				$notes  = array_map( array( alg_wc_pn()->core, 'sanitize_note' ), $_POST[ $id ] );
				$del    = ( isset( $_POST[ $id . '_del' ] ) ? array_map( 'sanitize_textarea_field', $_POST[ $id . '_del' ] ) : array() );
				alg_wc_pn()->core->set_product_notes( $notes, $private_or_public, $product_id, $del );
			}
			if ( 'yes' === get_option( "alg_wc_pn_{$private_or_public}_product_tab_title_per_product", 'no' ) ) {
				$tab_title_id = $id . '_tab_title';
				if ( isset( $_POST[ $tab_title_id ] ) && empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
					update_post_meta( $product_id, $tab_title_id, sanitize_textarea_field( $_POST[ $tab_title_id ] ) );
				}
			}
		}
	}

}

endif;

return new Alg_WC_Product_Notes_Edit();
