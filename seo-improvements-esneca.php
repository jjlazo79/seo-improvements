<?php

/**
 * SEO Improvements
 *
 * @subpackage        WordPress
 * @package           SEOImprovements
 * @author            iSocialweb - Jose Lazo
 * @copyright         2021 Jose Lazo
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       SEO Improvements
 * Plugin URI:        https://isocialweb.agency/plugins/seo-improvements
 * Description:       This plugin changes the pagination of WooCommerce products to Roger's pagination. From page 1 it links only to page 2 (href and next). From page 2 it links to everything (except page 2). From page 3 onwards it links to page 1, previous and next. Also add post order in category pages.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            iSocialweb - Jose Lazo
 * Author URI:        https://isocialweb.agency/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seo-improvements
 * Domain Path:       /languages
 */


// Exit if accessed directly.
defined('ABSPATH') or die('Bad dog. No biscuit!');
// Define some constants plugin.
define('SEOIMPROVEMENTS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('SEOIMPROVEMENTS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SEOIMPROVEMENTS_VERSION', '1.0.0');
define('SEOIMPROVEMENTS_TEXT_DOMAIN', 'seo-improvements');

// Initialize the plugin
$seo_improvements_plugin = new SEOImprovementsPlugin();

class SEOImprovementsPlugin
{

	/**
	 * Initializes the plugin.
	 *
	 * To keep the initialization fast, only add filter and action
	 * hooks in the constructor.
	 */
	public function __construct()
	{
		// Include classes.
		require 'classes/class-SEOImprovementsPagination.php';
		require 'classes/class-SEOImprovementsTopPosts.php';
		// require 'classes/class-SEOImprovementsPostType.php';
		// require 'classes/class-SEOImprovementsShortcode.php';

		// Actions.
		add_action('init', array($this, 'seo_improvements_plugin_localize_scripts'));
		add_action('plugins_loaded', array('SEOImprovementsPagination', 'get_instance'));
		add_action('plugins_loaded', array('SEOImprovementsTopPosts', 'get_instance'));
		// add_action('plugins_loaded', array('SEOImprovementsPostType', 'get_instance'));
		// add_action('plugins_loaded', array('SEOImprovementsShortcode', 'get_instance'));
	}

	/**
	 * Activation hook
	 *
	 * @return void
	 */
	public static function seo_improvements_plugin_activation()
	{
		if (!current_user_can('activate_plugins')) return;
		// First to register page template
		// $SEOImprovementsPage = SEOImprovementsPage::get_instance();
		// Create page and add the new page template
		// $add_new_page = SEOImprovementsPlugin::add_new_page();

		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules();
	}

	/**
	 * Deactivation hook
	 *
	 * @return void
	 */
	public static function seo_improvements_plugin_deactivation()
	{
		// Unregister the post type and taxonomies, so the rules are no longer in memory.
		unregister_post_type('seo-improvements');
		// unregister_taxonomy('custom-taxonomy');
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
	}

	/**
	 * Unistall hook
	 *
	 * @return void
	 */
	public static function seo_improvements_plugin_uninstall()
	{
		// Delete options
		delete_option('seo_improvements_options');
	}

	/**
	 * Localize path folder
	 *
	 * @return void
	 */
	public function seo_improvements_plugin_localize_scripts()
	{
		$domain = SEOIMPROVEMENTS_TEXT_DOMAIN;
		$locale = apply_filters('plugin_locale', get_locale(), $domain);
		load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages');
	}
}

// Activation, deactivation and uninstall plugin hooks
register_activation_hook(__FILE__, array('SEOImprovementsPlugin', 'seo_improvements_plugin_activation'));
register_deactivation_hook(__FILE__,  array('SEOImprovementsPlugin', 'seo_improvements_plugin_deactivation'));
register_uninstall_hook(__FILE__,  array('SEOImprovementsPlugin', 'seo_improvements_plugin_uninstall'));
