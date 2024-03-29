<?php
/**
 * Plugin Name: WP-Parsidate
 * Version: 3.1.1
 * Plugin URI: http://forum.wp-parsi.com/
 * Description: Persian package for WordPress, Adds full RTL and Shamsi (Jalali) support for: posts, comments, pages, archives, search, categories, permalinks and all admin sections and TinyMce editor, lists, quick editor. This package has Jalali archive widget.
 * Author: WP-Parsi Team
 * Author URI: http://wp-parsi.com/
 * Text Domain: wp-parsidate
 * Domain Path: parsi-languages
 * License: GPL3
 *
 * WP-Parsidate is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP-Parsidate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP-Parsidate.
 *
 *
 * WordPress Parsi Package, Adds Persian language & Jalali date support to your blog
 *
 * Developers:
 *              Mobin Ghasempoor ( Senior programmer & Founder )
 *              Morteza Geransayeh ( Senior programmer & Manager )
 *              Alireza Dabiri Nejad ( Programmer )
 *              Ehsaan ( Programmer )
 *              Farhan Nisi ( Programmer )
 *              Parsa Kafi ( Programmer )
 *              Mostafa Soufi ( Programmer )
 *              Ali Aghdam ( Programmer )
 *              Kamran Khorsandi ( Programmer )
 *              Mehrshad Darzi ( Programmer )
 *              Saeed Fard ( Analyst )
 *
 * @author              Mobin Ghasempoor
 * @author              Morteza Geransayeh
 * @author              Alireza Dabiri Nejad
 * @link                http://wp-parsi.com/
 * @version             3.0.1
 * @license             http://www.gnu.org/licenses/gpl-3.0.html GNU Public License v3.0
 * @package             WP-Parsidate
 * @subpackage          Core
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // No direct access allowed




add_action( 'init', 'github_plugin_updater_test_init' );
function github_plugin_updater_test_init() {
	include_once 'updater.php';
	define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'wp-parsidate',
			'api_url' => 'https://api.github.com/repos/nazi77/wp-parsidate',
			'raw_url' => 'https://raw.github.com/nazi77/wp-parsidate/master',
			'github_url' => 'https://github.com/nazi77/wp-parsidate',
			'zip_url' => 'https://github.com/nazi77/wp-parsidate/archive/master.zip',
			'sslverify' => true,
			'requires' => '4.0',
			'tested' => '5.2.2',
			'readme' => 'README.md',
			'access_token' => 'ec58cd600b5cdb0a2606690cc8fd5e1769e06c2b',
		);
		new WP_GitHub_Updater( $config );
	}
}






final class WP_Parsidate {
	/**
	 * @var WP_Parsidate Class instance
	 */
	public static $instance = null;

	private function __construct() {
		$this->define_const();
		$this->setup_vars();
		$this->include_files();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'parsi_settings_link' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Sets up constants for plugin
	 *
	 * @since           2.0
	 * @return          void
	 */
	private function define_const() {
		if ( ! defined( 'WP_PARSI_ROOT' ) ) {
			define( 'WP_PARSI_ROOT', __FILE__ );
		}

		if ( ! defined( 'WP_PARSI_DIR' ) ) {
			define( 'WP_PARSI_DIR', plugin_dir_path( WP_PARSI_ROOT ) );
		}

		if ( ! defined( 'WP_PARSI_URL' ) ) {
			define( 'WP_PARSI_URL', plugin_dir_url( WP_PARSI_ROOT ) );
		}

		if ( ! defined( 'WP_PARSI_VER' ) ) {
			define( 'WP_PARSI_VER', '2.2' );
		}
	}

	/**
	 * Sets up global variables
	 *
	 * @since           2.0
	 * @return          void
	 */
	private function setup_vars() {
		global $persian_month_names;
		$persian_month_names = array(
			'',
			'فروردین',
			'اردیبهشت',
			'خرداد',
			'تیر',
			'مرداد',
			'شهریور',
			'مهر',
			'آبان',
			'آذر',
			'دی',
			'بهمن',
			'اسفند'
		);
	}

	/**
	 * Includes files for plugin
	 *
	 * @since          2.0
	 * @return         void
	 */
	public function include_files() {
		require_once( WP_PARSI_DIR . 'includes/settings.php' );
		global $wpp_settings;
		$wpp_settings = wp_parsi_get_settings();

		$files = array(
			'parsidate',
			'general',
			'fixes-archive',
			'fixes-permalinks',
			'fixes-dates',
			'fixes-misc',
			'admin/styles-fix',
			'admin/datepicker-rtl',
			'admin/gutenberg-jalali-calendar',
			'admin/lists-fix',
			'admin/widgets',
			'fixes-calendar',
			'fixes-archives',
			'plugins/woocommerce',
			'plugins/fixes-woo',
			'plugins/edd',
			'plugins/disable',
			'widget/widget_archive',
			'widget/widget_calendar'
		);

		foreach ( $files as $file ) {
			require_once( WP_PARSI_DIR . 'includes/' . $file . '.php' );
		}

		if ( get_locale() == 'fa_IR' ) {
			load_textdomain( 'wp-parsidate', WP_PARSI_DIR . 'languages/fa_IR.mo' );
		}
	}

	/**
	 * Returns an instance of WP_Parsidate class, makes instance if not exists
	 *
	 * @since           2.0
	 * @return          WP_Parsidate Instance of WP_Parsidate
	 */
	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Parsidate();
		}

		return self::$instance;
	}

	/**
	 * Add Setting Link To Install Plugin
	 *
	 * @param           array $links
	 *
	 * @return          array
	 */
	public static function parsi_settings_link( $links ) {
		$settings_link = array( '<a href="' . menu_page_url( 'wp-parsi-settings', false ) . '">' . __( 'settings', 'wp-parsidate' ) . '</a>' );

		return array_merge( $links, $settings_link );
	}

	/**
	 * Register Plugin Widgets
	 *
	 * @since           2.0
	 * @return          boolean
	 */
	public function register_widget() {
		register_widget( 'parsidate_archive' );
		register_widget( 'parsidate_calendar' );

		return true;
	}
}

return WP_Parsidate::get_instance();


