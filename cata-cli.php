<?php
/**
 * Cata CLI
 *
 * @package   Cata\CLI
 * @author    Thought & Expression Co. <devjobs@thought.is>
 * @copyright 2022 Thought & Expression Co.
 * @license   GNU GENERAL PUBLIC LICENSE
 *
 * @wordpress-plugin
 * Plugin Name: Cata CLI
 * Description: WP CLI Commands to support sites using cata parent theme.
 * Author:      Thought & Expression Co. <devjobs@thought.is>
 * Author URI:  https://thought.is
 * Version:     0.1.0
 * License:     GPL v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init CLI
 */
function cata_init_cli() : void {
	/**
	 * Meta
	 */
	require_once __DIR__ . '/includes/meta/class-meta.php';

	WP_CLI::add_command( 'cata', 'Cata\\CLI\\Meta' );
}

if ( class_exists( 'WP_CLI' ) ) {
	add_action( 'init', 'cata_init_cli' );
}
