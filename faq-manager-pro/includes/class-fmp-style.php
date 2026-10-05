<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Appearance field definitions, presets and CSS-variable output. */
class FMP_Style {

	const OPTION = 'fmp_style';

	/** key => [label, type(color|number|text|select|check), default, var, unit, options, group] */
	public static function fields() {
		$shadows = array(
			'none'                         => __( 'None', 'faq-manager-pro' ),
			'0 2px 8px rgba(0,0,0,.08)'    => __( 'Soft', 'faq-manager-pro' ),
			'0 8px 24px rgba(0,0,0,.18)'   => __( 'Strong', 'faq-manager-pro' ),
		);
		$G = array(
			'colors'  => __( 'Colors', 'faq-manager-pro' ),
			'border'  => __( 'Border', 'faq-manager-pro' ),
			'font'    => __( 'Font', 'faq-manager-pro' ),
			'icon'    => __( 'Open / close icon', 'faq-manager-pro' ),
			'spacing' => __( 'Spacing & effects', 'faq-manager-pro' ),
		);
		return array(
			'bg'         => array( __( 'Wrapper background', 'faq-manager-pro' ), 'color', '#ffffff', '--fmp-bg', '', null, $G['colors'] ),
			'q_bg'       => array( __( 'Question background', 'faq-manager-pro' ), 'color', '#f8fafc', '--fmp-q-bg', '', null, $G['colors'] ),
			'q_hover_bg' => array( __( 'Question hover background', 'faq-manager-pro' ), 'color', '#eef2ff', '--fmp-q-hover-bg', '', null, $G['colors'] ),
			'q_active_bg'=> array( __( 'Question open (active) background', 'faq-manager-pro' ), 'color', '#e0e7ff', '--fmp-q-active-bg', '', null, $G['colors'] ),
			'a_bg'       => array( __( 'Answer background', 'faq-manager-pro' ), 'color', '#ffffff', '--fmp-a-bg', '', null, $G['colors'] ),
			'q_color'    => array( __( 'Question text color', 'faq-manager-pro' ), 'color', '#0f172a', '--fmp-q-color', '', null, $G['colors'] ),
			'q_hover_color' => array( __( 'Question hover text color', 'faq-manager-pro' ), 'color', '#3730a3', '--fmp-q-hover-color', '', null, $G['colors'] ),
			'a_color'    => array( __( 'Answer text color', 'faq-manager-pro' ), 'color', '#334155', '--fmp-a-color', '', null, $G['colors'] ),
			'link'       => array( __( 'Link color', 'faq-manager-pro' ), 'color', '#4f46e5', '--fmp-link', '', null, $G['colors'] ),
			'link_hover' => array( __( 'Link hover color', 'faq-manager-pro' ), 'color', '#312e81', '--fmp-link-hover', '', null, $G['colors'] ),

			'border_color' => array( __( 'Border color', 'faq-manager-pro' ), 'color', '#e2e8f0', '--fmp-border-color', '', null, $G['border'] ),
			'border_width' => array( __( 'Border width (px)', 'faq-manager-pro' ), 'number', 1, '--fmp-border-width', 'px', array( 0, 10 ), $G['border'] ),
			'border_style' => array( __( 'Border style', 'faq-manager-pro' ), 'select', 'solid', '--fmp-border-style', '', array( 'solid' => 'solid', 'dashed' => 'dashed', 'dotted' => 'dotted', 'double' => 'double', 'none' => 'none' ), $G['border'] ),
			'radius'       => array( __( 'Border radius (px)', 'faq-manager-pro' ), 'number', 10, '--fmp-radius', 'px', array( 0, 50 ), $G['border'] ),

			'font_family'  => array( __( 'Font family (CSS value, e.g. Vazirmatn, sans-serif)', 'faq-manager-pro' ), 'text', 'inherit', '--fmp-font', '', null, $G['font'] ),
			'google_font'  => array( __( 'Google Font name to load (optional)', 'faq-manager-pro' ), 'text', '', '', '', null, $G['font'] ),
			'q_size'       => array( __( 'Question font size (px)', 'faq-manager-pro' ), 'number', 17, '--fmp-q-size', 'px', array( 10, 40 ), $G['font'] ),
			'a_size'       => array( __( 'Answer font size (px)', 'faq-manager-pro' ), 'number', 15, '--fmp-a-size', 'px', array( 10, 40 ), $G['font'] ),
			'q_weight'     => array( __( 'Question font weight', 'faq-manager-pro' ), 'select', '600', '--fmp-q-weight', '', array( '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800' ), $G['font'] ),
			'line_height'  => array( __( 'Line height', 'faq-manager-pro' ), 'number', 1.7, '--fmp-line', '', array( 1, 3 ), $G['font'] ),

			'icon'         => array( __( 'Icon set', 'faq-manager-pro' ), 'select', 'plus', '', '', array( 'plus' => '+ / −', 'chevron' => '▾ chevron', 'arrow' => '→ arrow' ), $G['icon'] ),
			'icon_pos'     => array( __( 'Icon position', 'faq-manager-pro' ), 'select', 'right', '', '', array( 'right' => __( 'End', 'faq-manager-pro' ), 'left' => __( 'Start', 'faq-manager-pro' ) ), $G['icon'] ),
			'icon_color'   => array( __( 'Icon color', 'faq-manager-pro' ), 'color', '#4f46e5', '--fmp-icon-color', '', null, $G['icon'] ),
			'icon_size'    => array( __( 'Icon size (px)', 'faq-manager-pro' ), 'number', 22, '--fmp-icon-size', 'px', array( 8, 48 ), $G['icon'] ),

			'pad_y'        => array( __( 'Question vertical padding (px)', 'faq-manager-pro' ), 'number', 16, '--fmp-pad-y', 'px', array( 0, 60 ), $G['spacing'] ),
			'pad_x'        => array( __( 'Horizontal padding (px)', 'faq-manager-pro' ), 'number', 20, '--fmp-pad-x', 'px', array( 0, 60 ), $G['spacing'] ),
			'gap'          => array( __( 'Gap between items (px)', 'faq-manager-pro' ), 'number', 10, '--fmp-gap', 'px', array( 0, 60 ), $G['spacing'] ),
			'shadow'       => array( __( 'Shadow', 'faq-manager-pro' ), 'select', '0 2px 8px rgba(0,0,0,.08)', '--fmp-shadow', '', $shadows, $G['spacing'] ),
			'speed'        => array( __( 'Animation speed (ms)', 'faq-manager-pro' ), 'number', 300, '--fmp-speed', 'ms', array( 0, 1500 ), $G['spacing'] ),
		);
	}

	public static function presets() {
		return array(
			'minimal' => array( 'label' => __( 'Minimal', 'faq-manager-pro' ), 'values' => array(
				'bg' => '#ffffff', 'q_bg' => '#ffffff', 'q_hover_bg' => '#f8fafc', 'q_active_bg' => '#ffffff', 'a_bg' => '#ffffff',
				'q_color' => '#111827', 'q_hover_color' => '#000000', 'a_color' => '#4b5563', 'border_color' => '#e5e7eb', 'border_width' => 1,
				'radius' => 0, 'shadow' => 'none', 'icon' => 'plus', 'icon_color' => '#111827', 'gap' => 0, 'link' => '#111827', 'link_hover' => '#6b7280' ) ),
			'card' => array( 'label' => __( 'Card', 'faq-manager-pro' ), 'values' => array(
				'bg' => '#f1f5f9', 'q_bg' => '#ffffff', 'q_hover_bg' => '#eef2ff', 'q_active_bg' => '#e0e7ff', 'a_bg' => '#ffffff',
				'q_color' => '#0f172a', 'q_hover_color' => '#3730a3', 'a_color' => '#334155', 'border_color' => '#e2e8f0', 'border_width' => 1,
				'radius' => 14, 'shadow' => '0 2px 8px rgba(0,0,0,.08)', 'icon' => 'chevron', 'icon_color' => '#4f46e5', 'gap' => 12, 'link' => '#4f46e5', 'link_hover' => '#312e81' ) ),
			'dark' => array( 'label' => __( 'Dark', 'faq-manager-pro' ), 'values' => array(
				'bg' => '#0b1020', 'q_bg' => '#161d33', 'q_hover_bg' => '#1f2847', 'q_active_bg' => '#26305a', 'a_bg' => '#111830',
				'q_color' => '#f1f5f9', 'q_hover_color' => '#ffffff', 'a_color' => '#cbd5e1', 'border_color' => '#2b3556', 'border_width' => 1,
				'radius' => 12, 'shadow' => '0 8px 24px rgba(0,0,0,.18)', 'icon' => 'arrow', 'icon_color' => '#818cf8', 'gap' => 10, 'link' => '#a5b4fc', 'link_hover' => '#e0e7ff' ) ),
		);
	}

	public static function get() {
		$saved = get_option( self::OPTION, array() );
		$out   = array();
		foreach ( self::fields() as $k => $f ) {
			$out[ $k ] = isset( $saved[ $k ] ) ? $saved[ $k ] : $f[2];
		}
		$out['custom_css'] = isset( $saved['custom_css'] ) ? $saved['custom_css'] : '';
		return $out;
	}

	public static function sanitize( $in ) {
		$out = array();
		$in  = is_array( $in ) ? $in : array();
		foreach ( self::fields() as $k => $f ) {
			$v = isset( $in[ $k ] ) ? $in[ $k ] : $f[2];
			switch ( $f[1] ) {
				case 'color':  $v = sanitize_hex_color( $v ) ?: $f[2]; break;
				case 'number': $v = max( $f[5][0], min( $f[5][1], (float) $v ) ); break;
				case 'select': $v = array_key_exists( $v, $f[5] ) ? $v : $f[2]; break;
				default:       $v = trim( preg_replace( '/[^\p{L}\p{N}\s,\-_"\']/u', '', (string) $v ) );
			}
			$out[ $k ] = $v;
		}
		$out['custom_css'] = isset( $in['custom_css'] ) ? wp_strip_all_tags( $in['custom_css'] ) : '';
		return $out;
	}

	/** Inline style for the wrapper (CSS variables). */
	public static function vars_css( $style = null ) {
		$style = $style ?: self::get();
		$css   = '';
		foreach ( self::fields() as $k => $f ) {
			if ( $f[3] ) {
				$css .= $f[3] . ':' . $style[ $k ] . $f[4] . ';';
			}
		}
		return $css;
	}

	public static function font_url( $style ) {
		if ( empty( $style['google_font'] ) ) { return ''; }
		return 'https://fonts.googleapis.com/css2?family=' . rawurlencode( $style['google_font'] ) . ':wght@400;500;600;700;800&display=swap';
	}
}
