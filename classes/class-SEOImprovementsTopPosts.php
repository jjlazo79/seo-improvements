<?php

/**
 * Class SEOImprovementsTopPosts
 *
 * Handles the creation of a "SEOImprovementsTopPosts" page
 *
 * @subpackage WordPress
 * @package SEOImprovementss
 */
class SEOImprovementsTopPosts
{

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;


	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance()
	{

		if (null == self::$instance) {
			self::$instance = new SEOImprovementsTopPosts();
		}

		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct()
	{
		// Add Actions.
		add_action( 'add_meta_boxes', array($this, 'seo_improvements_register_metaboxes'));
		add_action( 'save_post',      array($this, 'seo_improvements_save_metaboxes'));
		add_action( 'pre_get_posts',  array($this, 'seo_improvements_category_order') );
	}

	/**
	 * Register metabox to Posts
	 *
	 * @return void
	 */
	public function seo_improvements_register_metaboxes()
	{
		add_meta_box(
			'si-meta-box',
			__('Order post in category pages', SEOIMPROVEMENTS_TEXT_DOMAIN),
			array($this, 'render_seo_improvements_metabox'),
			'post',
			'advanced',
			'default'
		);
	}

	/**
	 * Renders the Objects Comparison meta box.
	 *
	 * @param object $meta_id
	 * @return void
	 */
	public function render_seo_improvements_metabox($meta_id)
	{
		$outline = '<label for="si_post_order" style="width:150px; display:inline-block;">' . __( 'Post order', SEOIMPROVEMENTS_TEXT_DOMAIN ) . '</label>';
		$outline .= '<input type="number" name="si_post_order" id="si_post_order" class="si_post_order" min="0" max="100" steps="1" value="' . esc_attr(get_post_meta($meta_id->ID, 'si_post_order', true)) . '"/>';
		$outline .= '<small>This sort is only valid to category pages. Larger numbers will be displayed first.</small>';

		// Add nonce for security and authentication.
		wp_nonce_field('custom_nonce_action', 'custom_nonce');
		echo $outline;
	}

	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function seo_improvements_save_metaboxes($post_id)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		// Add nonce for security and authentication.
		$nonce_name   = isset($_POST['custom_nonce']) ? $_POST['custom_nonce'] : '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is valid.
		if (!wp_verify_nonce($nonce_name, $nonce_action)) {
			return;
		}

		// Check if user has permissions to save data.
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		// Check if not an autosave.
		if (wp_is_post_autosave($post_id)) {
			return;
		}

		// Check if not a revision.
		if (wp_is_post_revision($post_id)) {
			return;
		}

		// Check if $_POST field(s) are available
		if (isset($_POST['si_post_order'])) {
			$si_post_order = sanitize_text_field($_POST['si_post_order']);
			update_post_meta($post_id, 'si_post_order', $si_post_order);
		}
	}


	/**
	 * Set orderby postmeta post_order
	 *
	 * @param object $query
	 * @return void
	 */
	public function seo_improvements_category_order( $query ) {
		// Only in categoy pages.
		if ( !is_admin() && $query->is_category() && $query->is_main_query() ) {
			$query->set('meta_query', array(
				'relation' => 'OR',
				'exists_clause' => array(
					'key'     => 'si_post_order',
					'compare' => 'EXISTS'
					),
				'not_exists_clause' => array(
					'key'     => 'si_post_order',
					'compare' => 'NOT EXISTS'
					)
				)
			);
			$query->set('order', 'DESC');
			$query->set('orderby', 'not_exists_clause date');
		}
		return $query;
	}

}
