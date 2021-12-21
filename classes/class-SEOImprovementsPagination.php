<?php

/**
 * Class SEOImprovementsPagination
 *
 * Handles WooCommerce pagination
 *
 * @subpackage WordPress
 * @package SEOImprovements
 */
class SEOImprovementsPagination
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
			self::$instance = new SEOImprovementsPagination();
		}

		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct()
	{
		add_action('woocommerce_after_shop_loop', array($this, 'seo_improvements_pagination'), 10);
		add_action('wp', array($this, 'seo_improvements_plugin_remove_default_pagination'));
	}

	/**
	 * Remove default pagination
	 *
	 * @return void
	 */
	public function seo_improvements_plugin_remove_default_pagination(){
		remove_action('woocommerce_pagination', 'woocommerce_pagination ', 10);
		remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
	}


	/**
	* Custom pagination for better SEO index
	*
	* @return void
	*/
	public function seo_improvements_pagination()
	{

		if (is_singular())
			return;

		global $wp_query;

		$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
		$max   = intval($wp_query->max_num_pages);
		$links = array();

		// Start pagination with nav
		echo '<nav class="woocommerce-pagination">' . "\n" .
			'<ul class="page-numbers">' . "\n";

		/** Previous Post Link */
		if (get_previous_posts_link())
			printf('<li>%s</li>' . "\n", get_previous_posts_link('←'));

		/** Always link to first page */
		if (!in_array(1, $links) && ($paged != 1) && ($paged != 2)) {
			printf('<li><a class="page-numbers" href="%s">%s</a></li>' . "\n", esc_url(get_pagenum_link(1)), '1');
		}

		/** Add current page and next to the array */
		if ($paged === 1) {

			$links[] = $paged;
			// $links[] = $paged + 1; // I don't undestand why ¯\(ツ)/¯, but Josevi said
		}

		/** Add all links to the array (Roger's way)*/
		if ($paged === 2) {

			echo '<li><span class="page-numbers dots">…</span></li>' . "\n";

			for ($i = 0; $i < $max; $i++) {
				$links[] = $paged + $i;
			}
		}

		/** Add the pages around the current page to the array */
		if ($paged >= 3) {

			echo '<li><span class="page-numbers dots">…</span></li>' . "\n";

			$links[] = $paged;
		}

		/** Sort links */
		sort($links);

		/** Display required links */
		foreach ((array) $links as $link) :

			// Hide links to avoid scared users
			$display = 'style="display:none;"';

			if ($paged === $link) {
				printf('<li><span aria-current="page" class="page-numbers current">%s</span></li>' . "\n", $paged);
			} else {
				printf('<li %s><a class="page-numbers" href="%s">%s</a></li>' . "\n", $display, esc_url(get_pagenum_link($link)), $link);
			}

		endforeach;

		/** Link to last page and ellipsis*/
		if (!in_array($max, $links)) {

			if (!in_array($max - 1, $links))
				echo '<li><span class="page-numbers dots">…</span></li>' . "\n";
		}

		/** Ellipsis in page 2 */
		if ($paged === 2) {

			echo '<li><span class="page-numbers dots">…</span></li>' . "\n";
		}

		/** Next Post Link */
		if (get_next_posts_link())
			printf('<li>%s</li>' . "\n", get_next_posts_link('→'));

		// Close pagination with nav
		echo '</ul>' . "\n" .
			'</nav>' . "\n";
	}

}
