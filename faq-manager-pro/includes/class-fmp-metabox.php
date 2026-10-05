<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class FMP_Metabox {
	const META = '_fmp_items';

	public static function post_types() {
		$all = array_keys( get_post_types( array( 'public' => true ) ) ); // includes ACF / CPT UI types
		$all = array_diff( $all, array( 'attachment', 'faq' ) );
		$sel = get_option( 'fmp_post_types', null );
		return is_array( $sel ) ? array_values( array_intersect( $all, $sel ) ) : array_values( $all );
	}

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add' ) );
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_action( 'wp_ajax_fmp_search', array( __CLASS__, 'ajax_search' ) );
	}

	public static function add() {
		foreach ( self::post_types() as $pt ) {
			add_meta_box( 'fmp_items', __( 'FAQs for this content', 'faq-manager-pro' ), array( __CLASS__, 'render' ), $pt, 'normal', 'default' );
		}
	}

	public static function assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) { return; }
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, self::post_types(), true ) ) { return; }
		wp_enqueue_style( 'fmp-admin', FMP_URL . 'assets/admin.css', array(), FMP_VERSION );
		wp_enqueue_script( 'fmp-admin', FMP_URL . 'assets/admin.js', array( 'jquery', 'jquery-ui-sortable' ), FMP_VERSION, true );
		$cats = array();
		foreach ( get_terms( array( 'taxonomy' => 'faq_category', 'hide_empty' => false ) ) as $t ) { $cats[] = array( $t->term_id, $t->name ); }
		wp_localize_script( 'fmp-admin', 'FMP', array(
			'ajax' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'fmp_search' ), 'cats' => $cats,
			'i' => array(
				'question' => __( 'Question', 'faq-manager-pro' ), 'answer' => __( 'Answer', 'faq-manager-pro' ),
				'nocat' => __( 'No category', 'faq-manager-pro' ), 'add' => __( 'Add', 'faq-manager-pro' ),
				'none' => __( 'No results', 'faq-manager-pro' ), 'archived' => __( 'Archived', 'faq-manager-pro' ),
				'new' => __( 'New', 'faq-manager-pro' ), 'remove' => __( 'Remove', 'faq-manager-pro' ),
			),
		) );
	}

	public static function render( $post ) {
		wp_nonce_field( 'fmp_save', 'fmp_nonce' );
		$ids = (array) get_post_meta( $post->ID, self::META, true );
		?>
		<div class="fmp-box">
			<div class="fmp-search">
				<strong><?php esc_html_e( 'Add an existing FAQ (archived included)', 'faq-manager-pro' ); ?></strong>
				<div class="fmp-search-row">
					<input type="search" id="fmp-q" placeholder="<?php esc_attr_e( 'Search FAQs…', 'faq-manager-pro' ); ?>">
					<?php wp_dropdown_categories( array( 'taxonomy' => 'faq_category', 'show_option_all' => __( 'All categories', 'faq-manager-pro' ), 'hide_empty' => 0, 'name' => 'fmp_cat_filter', 'id' => 'fmp-cat', 'hierarchical' => 1 ) ); ?>
					<select id="fmp-status">
						<option value="any"><?php esc_html_e( 'Any status', 'faq-manager-pro' ); ?></option>
						<option value="publish"><?php esc_html_e( 'Published', 'faq-manager-pro' ); ?></option>
						<option value="draft"><?php esc_html_e( 'Draft', 'faq-manager-pro' ); ?></option>
						<option value="archived"><?php esc_html_e( 'Archived', 'faq-manager-pro' ); ?></option>
					</select>
				</div>
				<ul id="fmp-results"></ul>
			</div>
			<ul id="fmp-list">
				<?php foreach ( $ids as $id ) :
					$p = $id ? get_post( (int) $id ) : null;
					if ( ! $p || 'faq' !== $p->post_type ) { continue; } ?>
					<li class="fmp-row" data-id="<?php echo (int) $id; ?>">
						<span class="dashicons dashicons-menu fmp-handle"></span>
						<input type="hidden" name="fmp_items[<?php echo (int) $id; ?>][id]" value="<?php echo (int) $id; ?>">
						<span class="fmp-title"><?php echo esc_html( $p->post_title ); ?>
							<?php if ( get_post_meta( $id, '_fmp_archived', true ) ) { echo '<em>(' . esc_html__( 'Archived', 'faq-manager-pro' ) . ')</em>'; } ?></span>
						<a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit', 'faq-manager-pro' ); ?></a>
						<button type="button" class="button-link fmp-remove">&times;</button>
					</li>
				<?php endforeach; ?>
			</ul>
			<p><button type="button" class="button" id="fmp-new"><?php esc_html_e( '+ Write a new FAQ', 'faq-manager-pro' ); ?></button></p>
		</div>
		<?php
	}

	public static function ajax_search() {
		check_ajax_referer( 'fmp_search', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error(); }
		$st   = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : 'any';
		$args = array( 'post_type' => 'faq', 'posts_per_page' => 20, 's' => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '', 'post_status' => array( 'publish', 'draft', 'pending', 'private' ) );
		if ( in_array( $st, array( 'publish', 'draft' ), true ) ) { $args['post_status'] = $st; }
		if ( 'archived' === $st ) { $args['meta_query'] = array( array( 'key' => '_fmp_archived', 'value' => '1' ) ); }
		if ( ! empty( $_GET['cat'] ) ) { $args['tax_query'] = array( array( 'taxonomy' => 'faq_category', 'terms' => (int) $_GET['cat'] ) ); }
		$out = array();
		foreach ( get_posts( $args ) as $p ) {
			$out[] = array( 'id' => $p->ID, 'title' => $p->post_title, 'archived' => (bool) get_post_meta( $p->ID, '_fmp_archived', true ) );
		}
		wp_send_json_success( $out );
	}

	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['fmp_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fmp_nonce'] ) ), 'fmp_save' ) ) { return; }
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) { return; }
		if ( ! in_array( $post->post_type, self::post_types(), true ) ) { return; }
		$rows = isset( $_POST['fmp_items'] ) && is_array( $_POST['fmp_items'] ) ? wp_unslash( $_POST['fmp_items'] ) : array();
		$ids  = array();
		foreach ( $rows as $r ) {
			if ( ! empty( $r['id'] ) ) {
				$fid = (int) $r['id'];
				if ( 'faq' === get_post_type( $fid ) ) { $ids[] = $fid; }
			} elseif ( ! empty( $r['question'] ) && current_user_can( 'publish_posts' ) ) {
				$fid = wp_insert_post( array(
					'post_type' => 'faq', 'post_status' => 'publish',
					'post_title' => sanitize_text_field( $r['question'] ), 'post_content' => wp_kses_post( $r['answer'] ?? '' ),
				) );
				if ( $fid && ! is_wp_error( $fid ) ) {
					if ( ! empty( $r['cat'] ) ) { wp_set_object_terms( $fid, array( (int) $r['cat'] ), 'faq_category' ); }
					$ids[] = $fid;
				}
			}
		}
		$ids ? update_post_meta( $post_id, self::META, array_values( array_unique( $ids ) ) ) : delete_post_meta( $post_id, self::META );
	}
}
