<?php
/**
 * Plugin Name: FAQ Manager Pro
 * Description: Professional FAQ manager: assign FAQs to any post type (incl. ACF/CPT UI), shortcode [faq_manager], fully customizable design with live preview.
 * Version: 1.0.0
 * Text Domain: faq-manager-pro
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'FMP_VERSION', '1.0.0' );
define( 'FMP_DIR', plugin_dir_path( __FILE__ ) );
define( 'FMP_URL', plugin_dir_url( __FILE__ ) );

require_once FMP_DIR . 'includes/class-fmp-style.php';
require_once FMP_DIR . 'includes/class-fmp-cpt.php';
require_once FMP_DIR . 'includes/class-fmp-metabox.php';
require_once FMP_DIR . 'includes/class-fmp-render.php';
require_once FMP_DIR . 'includes/class-fmp-settings.php';

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'faq-manager-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	FMP_CPT::init();
	FMP_Metabox::init();
	FMP_Render::init();
	FMP_Settings::init();
} );

register_activation_hook( __FILE__, function () {
	FMP_CPT::register();
	flush_rewrite_rules();
} );
