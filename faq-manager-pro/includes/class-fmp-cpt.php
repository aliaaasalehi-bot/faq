<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class FMP_CPT {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes_faq', array( __CLASS__, 'archive_box' ) );
		add_action( 'save_post_faq', array( __CLASS__, 'save_archive' ), 10, 2 );
		add_filter( 'manage_faq_posts_columns', function ( $c ) { $c['fmp_archived'] = __( 'Archived', 'faq-manager-pro' ); return $c; } );
		add_action( 'manage_faq_posts_custom_column', function ( $col, $id ) {
			if ( 'fmp_archived' === $col ) { echo get_post_meta( $id, '_fmp_archived', true ) ? '✔' : '—'; }
		}, 10, 2 );
	}

	public static function register() {
		register_post_type( 'faq', array(
			'labels' => array(
				'name' => __( 'FAQs', 'faq-manager-pro' ), 'singular_name' => __( 'FAQ', 'faq-manager-pro' ),
				'add_new_item' => __( 'Add new FAQ', 'faq-manager-pro' ), 'edit_item' => __( 'Edit FAQ', 'faq-manager-pro' ),
				'title_placeholder' => __( 'Question', 'faq-manager-pro' ),
			),
			'public' => false, 'show_ui' => true, 'show_in_menu' => true, 'menu_icon' => 'dashicons-editor-help',
			'supports' => array( 'title', 'editor' ), 'show_in_rest' => true, 'has_archive' => false,
		) );
		register_taxonomy( 'faq_category', 'faq', array(
			'labels' => array( 'name' => __( 'FAQ Categories', 'faq-manager-pro' ), 'singular_name' => __( 'FAQ Category', 'faq-manager-pro' ) ),
			'hierarchical' => true, 'public' => false, 'show_ui' => true, 'show_admin_column' => true, 'show_in_rest' => true,
		) );
	}

	public static function archive_box() {
		add_meta_box( 'fmp_archive', __( 'Archive', 'faq-manager-pro' ), function ( $post ) {
			wp_nonce_field( 'fmp_archive', 'fmp_archive_nonce' );
			echo '<label><input type="checkbox" name="fmp_archived" value="1" ' . checked( get_post_meta( $post->ID, '_fmp_archived', true ), '1', false ) . '> ' . esc_html__( 'Archived (hidden from category listings, can still be attached manually)', 'faq-manager-pro' ) . '</label>';
		}, 'faq', 'side' );
	}

	public static function save_archive( $id, $post ) {
		if ( ! isset( $_POST['fmp_archive_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fmp_archive_nonce'] ) ), 'fmp_archive' ) ) { return; }
		if ( ! current_user_can( 'edit_post', $id ) ) { return; }
		empty( $_POST['fmp_archived'] ) ? delete_post_meta( $id, '_fmp_archived' ) : update_post_meta( $id, '_fmp_archived', '1' );
	}
}
