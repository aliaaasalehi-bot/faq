<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class FMP_Settings {
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function menu() {
		add_submenu_page( 'edit.php?post_type=faq', __( 'Appearance', 'faq-manager-pro' ), __( 'Appearance', 'faq-manager-pro' ), 'manage_options', 'fmp-appearance', array( __CLASS__, 'page_appearance' ) );
		add_submenu_page( 'edit.php?post_type=faq', __( 'Settings', 'faq-manager-pro' ), __( 'Settings', 'faq-manager-pro' ), 'manage_options', 'fmp-settings', array( __CLASS__, 'page_settings' ) );
	}

	public static function register() {
		register_setting( 'fmp_style_group', FMP_Style::OPTION, array( 'sanitize_callback' => array( 'FMP_Style', 'sanitize' ) ) );
		register_setting( 'fmp_settings_group', 'fmp_post_types', array( 'sanitize_callback' => function ( $v ) {
			$valid = array_keys( get_post_types( array( 'public' => true ) ) );
			return is_array( $v ) ? array_values( array_intersect( array_map( 'sanitize_key', $v ), $valid ) ) : array();
		} ) );
	}

	public static function assets( $hook ) {
		if ( 'faq_page_fmp-appearance' !== $hook ) { return; }
		$style = FMP_Style::get();
		wp_enqueue_style( 'fmp-front', FMP_URL . 'assets/front.css', array(), FMP_VERSION );
		if ( $url = FMP_Style::font_url( $style ) ) { wp_enqueue_style( 'fmp-font', $url, array(), null ); }
		wp_enqueue_style( 'fmp-admin', FMP_URL . 'assets/admin.css', array(), FMP_VERSION );
		wp_enqueue_script( 'fmp-front', FMP_URL . 'assets/front.js', array(), FMP_VERSION, true );
		wp_enqueue_script( 'fmp-appearance', FMP_URL . 'assets/appearance.js', array( 'jquery' ), FMP_VERSION, true );
		$fields = array();
		foreach ( FMP_Style::fields() as $k => $f ) { $fields[ $k ] = array( 'var' => $f[3], 'unit' => $f[4] ); }
		wp_localize_script( 'fmp-appearance', 'FMPA', array( 'fields' => $fields, 'presets' => FMP_Style::presets(), 'defaults' => wp_list_pluck( FMP_Style::fields(), 2 ) ) );
	}

	public static function page_settings() {
		$types = array_diff( array_keys( get_post_types( array( 'public' => true ), 'names' ) ), array( 'attachment', 'faq' ) );
		$sel   = FMP_Metabox::post_types();
		echo '<div class="wrap"><h1>' . esc_html__( 'FAQ Settings', 'faq-manager-pro' ) . '</h1><form method="post" action="options.php">';
		settings_fields( 'fmp_settings_group' );
		echo '<h2>' . esc_html__( 'Post types where the FAQ box is enabled', 'faq-manager-pro' ) . '</h2><p>' . esc_html__( 'All public post types are listed, including those created with ACF / CPT UI.', 'faq-manager-pro' ) . '</p>';
		foreach ( $types as $t ) {
			$o = get_post_type_object( $t );
			echo '<p><label><input type="checkbox" name="fmp_post_types[]" value="' . esc_attr( $t ) . '" ' . checked( in_array( $t, $sel, true ), true, false ) . '> ' . esc_html( $o->labels->name ) . ' <code>' . esc_html( $t ) . '</code></label></p>';
		}
		echo '<p><b>' . esc_html__( 'Shortcode:', 'faq-manager-pro' ) . '</b> <code>[faq_manager]</code> — <code>post_id</code>, <code>category</code>, <code>limit</code>, <code>schema="yes|no"</code>, <code>open="first|all|none"</code>, <code>multiple="yes|no"</code></p>';
		submit_button();
		echo '</form></div>';
	}

	public static function page_appearance() {
		$style = FMP_Style::get();
		$name  = FMP_Style::OPTION;
		$demo  = array(
			array( __( 'How do I get started?', 'faq-manager-pro' ), '<p>' . __( 'Install the plugin, write your FAQs and attach them to any page. <a href="#">Read more</a>.', 'faq-manager-pro' ) . '</p>' ),
			array( __( 'Can I use it with custom post types?', 'faq-manager-pro' ), '<p>' . __( 'Yes, every public post type works, including ACF types.', 'faq-manager-pro' ) . '</p>' ),
			array( __( 'Is the design customizable?', 'faq-manager-pro' ), '<p>' . __( 'Everything: colors, borders, fonts, icons, hover states and more.', 'faq-manager-pro' ) . '</p>' ),
		);
		echo '<div class="wrap"><h1>' . esc_html__( 'FAQ Appearance', 'faq-manager-pro' ) . '</h1>';
		echo '<div class="fmp-appearance"><form method="post" action="options.php" class="fmp-form">';
		settings_fields( 'fmp_style_group' );
		echo '<p><b>' . esc_html__( 'Presets:', 'faq-manager-pro' ) . '</b> ';
		foreach ( FMP_Style::presets() as $k => $p ) { echo '<button type="button" class="button fmp-preset" data-preset="' . esc_attr( $k ) . '">' . esc_html( $p['label'] ) . '</button> '; }
		echo '<button type="button" class="button" id="fmp-reset">' . esc_html__( 'Reset to default', 'faq-manager-pro' ) . '</button></p>';
		$group = '';
		foreach ( FMP_Style::fields() as $k => $f ) {
			if ( $f[6] !== $group ) { echo '<h3>' . esc_html( $f[6] ) . '</h3>'; $group = $f[6]; }
			echo '<p class="fmp-field"><label>' . esc_html( $f[0] ) . '<br>';
			$n = esc_attr( "{$name}[{$k}]" );
			$v = esc_attr( $style[ $k ] );
			if ( 'select' === $f[1] ) {
				echo '<select name="' . $n . '" data-key="' . esc_attr( $k ) . '">';
				foreach ( $f[5] as $ov => $ol ) { echo '<option value="' . esc_attr( $ov ) . '" ' . selected( (string) $style[ $k ], (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>'; }
				echo '</select>';
			} elseif ( 'number' === $f[1] ) {
				echo '<input type="number" step="any" min="' . esc_attr( $f[5][0] ) . '" max="' . esc_attr( $f[5][1] ) . '" name="' . $n . '" value="' . $v . '" data-key="' . esc_attr( $k ) . '">';
			} else {
				echo '<input type="' . ( 'color' === $f[1] ? 'color' : 'text' ) . '" name="' . $n . '" value="' . $v . '" data-key="' . esc_attr( $k ) . '">';
			}
			echo '</label></p>';
		}
		echo '<h3>' . esc_html__( 'Custom CSS', 'faq-manager-pro' ) . '</h3><textarea name="' . esc_attr( $name ) . '[custom_css]" rows="6" class="large-text code" id="fmp-custom-css">' . esc_textarea( $style['custom_css'] ) . '</textarea>';
		submit_button( __( 'Save', 'faq-manager-pro' ) );
		echo '</form><div class="fmp-preview-pane"><h3>' . esc_html__( 'Live preview', 'faq-manager-pro' ) . '</h3>';
		echo '<style id="fmp-preview-css"></style>';
		echo '<div id="fmp-preview" style="' . esc_attr( FMP_Style::vars_css( $style ) ) . '">' . FMP_Render::markup( $demo, $style, 'first', false, 'fmp-preview-wrap' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<p class="description">' . esc_html__( 'Hover and click the items to preview hover and open states.', 'faq-manager-pro' ) . '</p></div></div></div>';
	}
}
