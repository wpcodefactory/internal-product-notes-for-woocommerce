/**
 * Product Notes for WooCommerce
 *
 * @version 2.5.1
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

jQuery( document ).ready( function() {

	/**
	 * add fields.
	 *
	 * @version 2.5.1
	 * @since   2.0.0
	 *
	 * @todo    [later] (dev) code refactoring, i.e. `foreach ( array( 'private', 'public' ) as $private_or_public )`
	 * @todo    [later] (dev) "delete" with AJAX
	 */
	jQuery( '#' + alg_wc_pn.private_id + '_add' ).click( function( event ) {
		var num = parseInt( jQuery( '#' + alg_wc_pn.private_id + '_num' ).val() );
		var ta  = '<textarea name="' + alg_wc_pn.private_id + '[' + num + ']" id="' + alg_wc_pn.private_id + '_' + num + '" class="' + alg_wc_pn.private_id + '"></textarea><br>';
		var del = '<input type="checkbox" name="' + alg_wc_pn.private_id + '_del[' + num + ']" id="' + alg_wc_pn.private_id + '_del_' + num + '"> ' +
			'<label for="' + alg_wc_pn.private_id + '_del_' + num + '">' + alg_wc_pn.delete_text + '</label>';
		jQuery( 'div.' + alg_wc_pn.private_id + '_wrap' ).prepend( '<hr>' + ta + del );
		if ( alg_wc_pn.private_is_wp_editor ) {
			wp.editor.initialize( alg_wc_pn.private_id + '_' + num, alg_wc_pn_get_wp_editor_settings() );
		}
		jQuery( '#' + alg_wc_pn.private_id + '_num' ).val( num + 1 );
	} );
	jQuery( '#' + alg_wc_pn.public_id + '_add' ).click( function( event ) {
		var num = parseInt( jQuery( '#' + alg_wc_pn.public_id + '_num' ).val() );
		var ta  = '<textarea name="' + alg_wc_pn.public_id + '[' + num + ']" id="' + alg_wc_pn.public_id + '_' + num + '" class="' + alg_wc_pn.public_id + '"></textarea><br>';
		var del = '<input type="checkbox" name="' + alg_wc_pn.public_id + '_del[' + num + ']" id="' + alg_wc_pn.public_id + '_del_' + num + '"> ' +
			'<label for="' + alg_wc_pn.public_id + '_del_' + num + '">' + alg_wc_pn.delete_text + '</label>';
		jQuery( 'div.' + alg_wc_pn.public_id + '_wrap' ).prepend( '<hr>' + ta + del );
		if ( alg_wc_pn.public_is_wp_editor ) {
			wp.editor.initialize( alg_wc_pn.public_id + '_' + num, alg_wc_pn_get_wp_editor_settings() );
		}
		jQuery( '#' + alg_wc_pn.public_id + '_num' ).val( num + 1 );
	} );

	/**
	 * alg_wc_pn_get_wp_editor_settings.
	 *
	 * @version 2.5.1
	 * @since   2.5.1
	 *
	 * @see     /wp-includes/class-wp-editor.php
	 * @see     https://codex.wordpress.org/Javascript_Reference/wp.editor
	 * @see     https://wordpress.stackexchange.com/questions/274592/how-to-create-wp-editor-using-javascript
	 * @see     https://make.wordpress.org/core/2017/05/20/editor-api-changes-in-4-8/
	 * @see     https://gist.github.com/rheinardkorf/aec4d46d3833d2f7a6a27c4481ba0b44
	 *
	 * @todo    [next] (dev) match settings with PHP `wp_editor()`, e.g. `dragDropUpload`, `rows`, etc.
	 */
	function alg_wc_pn_get_wp_editor_settings() {
		return {
			tinymce: {
				wpautop: true,
				plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
				toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link wp_more fullscreen',
			},
			quicktags: true,
			mediaButtons: true,
		}
	}

} );
