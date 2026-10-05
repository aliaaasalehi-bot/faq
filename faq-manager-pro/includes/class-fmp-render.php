<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class FMP_Render {
	public static function init() {
		add_shortcode( 'faq_manager', array( __CLASS__, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', function () {
			wp_register_style( 'fmp-front', FMP_URL . 'assets/front.css', array(), FMP_VERSION );
			wp_register_script( 'fmp-front', FMP_URL . 'assets/front.js', array(), FMP_VERSION, true );
		} );
	}

	public static function shortcode( $atts ) {
		$a = shortcode_atts( array( 'post_id' => 0, 'category' => '', 'limit' => 0, 'schema' => 'yes', 'open' => 'first', 'multiple' => 'no' ), $atts, 'faq_manager' );
		$items = array();
		if ( $a['category'] ) {
			$q = array( 'post_type' => 'faq', 'post_status' => 'publish', 'posts_per_page' => $a['limit'] ? (int) $a['limit'] : -1,
				'meta_query' => array( array( 'key' => '_fmp_archived', 'compare' => 'NOT EXISTS' ) ),
				'tax_query' => array( array( 'taxonomy' => 'faq_category', 'field' => is_numeric( $a['category'] ) ? 'term_id' : 'slug', 'terms' => array_map( 'trim', explode( ',', $a['category'] ) ) ) ) );
			$items = get_posts( $q );
		} else {
			$pid = (int) $a['post_id'] ?: get_the_ID();
			foreach ( (array) get_post_meta( $pid, FMP_Metabox::META, true ) as $id ) {
				$p = $id ? get_post( (int) $id ) : null;
				if ( $p && 'publish' === $p->post_status ) { $items[] = $p; }
			}
			if ( $a['limit'] ) { $items = array_slice( $items, 0, (int) $a['limit'] ); }
		}
		if ( ! $items ) { return ''; }
		$data = array();
		foreach ( $items as $p ) { $data[] = array( $p->post_title, apply_filters( 'the_content', $p->post_content ) ); }
		$style = FMP_Style::get();
		self::enqueue( $style );
		$html = self::markup( $data, $style, $a['open'], 'yes' === $a['multiple'] );
		if ( 'yes' === $a['schema'] ) { $html .= self::schema( $data ); }
		return $html;
	}

	public static function enqueue( $style ) {
		if ( $url = FMP_Style::font_url( $style ) ) { wp_enqueue_style( 'fmp-font', $url, array(), null ); }
		wp_enqueue_style( 'fmp-front' );
		wp_add_inline_style( 'fmp-front', '.fmp-wrap{' . FMP_Style::vars_css( $style ) . '}' . $style['custom_css'] );
		wp_enqueue_script( 'fmp-front' );
	}

	/** Shared markup used on the front end and in the admin preview. */
	public static function markup( $data, $style, $open = 'first', $multiple = false, $extra_class = '' ) {
		static $n = 0; $n++;
		$o = '<div class="fmp-wrap ' . esc_attr( $extra_class ) . '" data-icon="' . esc_attr( $style['icon'] ) . '" data-pos="' . esc_attr( $style['icon_pos'] ) . '" data-multiple="' . ( $multiple ? '1' : '0' ) . '">';
		foreach ( $data as $i => $d ) {
			$is_open = ( 'first' === $open && 0 === $i ) || 'all' === $open;
			$id = 'fmp-' . $n . '-' . $i;
			$o .= '<div class="fmp-item' . ( $is_open ? ' is-open' : '' ) . '">'
				. '<h3 class="fmp-q"><button type="button" class="fmp-btn" aria-expanded="' . ( $is_open ? 'true' : 'false' ) . '" aria-controls="' . $id . '"><span class="fmp-qt">' . esc_html( $d[0] ) . '</span><span class="fmp-icon" aria-hidden="true"></span></button></h3>'
				. '<div class="fmp-a" id="' . $id . '" role="region"><div class="fmp-a-inner"><div class="fmp-a-body">' . wp_kses_post( $d[1] ) . '</div></div></div></div>';
		}
		return $o . '</div>';
	}

	public static function schema( $data ) {
		$ent = array();
		foreach ( $data as $d ) { $ent[] = array( '@type' => 'Question', 'name' => wp_strip_all_tags( $d[0] ), 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_kses_post( $d[1] ) ) ); }
		return '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $ent ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
	}
}
