<?php
/**
 * Meta
 * 
 * @package Cata\CLI
 * @since 0.1.0
 */

namespace Cata\CLI;

use WP_CLI;
use Exception;

/**
 * Meta
 */
class Meta {

	/**
	 * Update post meta key
	 * 
	 * <currentkey>
	 * : The current meta_key
	 * 
	 * <newkey>
	 * : The new meta_key to use
	 * 
	 * [--dry_run=<boolean>]
	 * : Dry run.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 *
	 * [--post_type=<string>]
	 * : Specify post type to limit scope of deletion.
	 * ---
	 * default: 'any'
	 * ---
	 *
	 * @when after_wp_load
	 */
	public function update_post_meta_key( array $args, array $assoc_args ) : void {
		global $wpdb;

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'dry_run'   => 'false',
				'post_type' => 'any',
			)
		);

		$post_type = $assoc_args['post_type'];

		if ( 'any' !== $post_type && ! post_type_exists( $post_type ) ) {
			WP_CLI::log( "Post type {$post_type} not found, exiting." );
			return;
		}

		$filter = $this->post_type_filter( $post_type );

		$count = $wpdb->query(
			$wpdb->prepare(
				"SELECT meta_id FROM `$wpdb->postmeta` {$filter['join']} WHERE `$wpdb->postmeta`.`meta_key` = %s{$filter['where']}",
				sanitize_key( $args[0] ),
				...$filter['args']
			)
		);

		WP_CLI::log( "Found {$count} rows to update." );

		if ( self::is_dry_run( $assoc_args ) ) {
			return;
		}

		try {
			$result = $wpdb->query(
				$wpdb->prepare(
					"UPDATE `$wpdb->postmeta` {$filter['join']} SET `$wpdb->postmeta`.`meta_key` = %s WHERE `$wpdb->postmeta`.`meta_key` = %s{$filter['where']}",
					sanitize_key( $args[1] ),
					sanitize_key( $args[0] ),
					...$filter['args']
				)
			);

			WP_CLI::log( "Updated {$result} rows." );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Delete post meta
	 * 
	 * <key>
	 * : Post meta key
	 * 
	 * [--dry_run=<boolean>]
	 * : Dry run.
	 * ---
	 * default: false
	 * options:
	 *   - true
	 *   - false
	 * ---
	 * 
	 * [--post_type=<string>]
	 * : Specify post type to limit scope of deletion.
	 * ---
	 * default: 'any'
	 * ---
	 * 
	 * @when after_wp_load
	 */
	public function delete_post_meta( array $args, array $assoc_args ) : void {
		global $wpdb;

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'dry_run'   => 'false',
				'post_type' => 'any',
			)
		);

		$post_type = $assoc_args['post_type'];

		if ( 'any' !== $post_type && ! post_type_exists( $post_type ) ) {
			WP_CLI::log( "Post type {$post_type} not found, exiting." );
			return;
		}

		$filter = $this->post_type_filter( $post_type );

		$count = $wpdb->query(
			$wpdb->prepare(
				"SELECT meta_id FROM `$wpdb->postmeta` {$filter['join']} WHERE `$wpdb->postmeta`.`meta_key` = %s{$filter['where']}",
				sanitize_key( $args[0] ),
				...$filter['args']
			)
		);

		WP_CLI::log( "Found {$count} rows to delete." );

		$select  = ( 'any' === $post_type ) ? '' : ", `$wpdb->posts`.`post_type`";
		$columns = ( 'any' === $post_type )
			? array( 'meta_id', 'post_id', 'meta_key', 'meta_value' )
			: array( 'post_type', 'meta_id', 'post_id', 'meta_key', 'meta_value' );

		$examples = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `$wpdb->postmeta`.`meta_id`, `$wpdb->postmeta`.`post_id`, `$wpdb->postmeta`.`meta_key`, `$wpdb->postmeta`.`meta_value`{$select} FROM `$wpdb->postmeta` {$filter['join']} WHERE `$wpdb->postmeta`.`meta_key` = %s{$filter['where']} LIMIT 10",
				sanitize_key( $args[0] ),
				...$filter['args']
			)
		);

		WP_CLI::log( "Here are the first 10 results for reference." );

		WP_CLI\Utils\format_items( 'table', $examples, $columns );

		if ( self::is_dry_run( $assoc_args ) ) {
			return;
		}
		try {
			$result = $wpdb->query(
				$wpdb->prepare(
					"DELETE `$wpdb->postmeta` FROM `$wpdb->postmeta` {$filter['join']} WHERE `$wpdb->postmeta`.`meta_key` = %s{$filter['where']}",
					sanitize_key( $args[0] ),
					...$filter['args']
				)
			);
			WP_CLI::log( "Deleted {$result} rows." );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Build the post_type filter fragments for a postmeta query.
	 *
	 * Returns the JOIN clause, the trailing WHERE condition, and any extra
	 * prepare() arguments needed to scope a query to a single post type.
	 * For 'any' the fragments are empty, leaving the base query unfiltered.
	 *
	 * @param string $post_type Post type slug, or 'any' for no filter.
	 * @return array{join:string, where:string, args:array}
	 */
	private function post_type_filter( string $post_type ) : array {
		global $wpdb;

		if ( 'any' === $post_type ) {
			return array(
				'join'  => '',
				'where' => '',
				'args'  => array(),
			);
		}

		return array(
			'join'  => "JOIN `$wpdb->posts` ON `$wpdb->posts`.`ID` = `$wpdb->postmeta`.`post_id`",
			'where' => " AND `$wpdb->posts`.`post_type` = %s",
			'args'  => array( sanitize_key( $post_type ) ),
		);
	}

	/**
	 * Is Dry Run
	 *
	 * @param array $assoc_args
	 * @return bool
	 */
	private static function is_dry_run( array $assoc_args ) : bool {
		return 'true' === $assoc_args['dry_run'];
	}
}
