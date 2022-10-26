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
	 * @when after_wp_load
	 */
	public function update_post_meta_key( array $args, array $assoc_args ) : void {
		global $wpdb;

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'dry_run' => 'false',
			)
		);

		$count = $wpdb->query(
			$wpdb->prepare(
				"SELECT * FROM `$wpdb->postmeta` WHERE meta_key = %s",
				sanitize_key( $args[0] )
			)
		);

		WP_CLI::log( "Found {$count} rows to update." );

		if ( self::is_dry_run( $assoc_args ) ) {
			return;
		}

		try {
			$result = $wpdb->query(
				$wpdb->prepare(
					"UPDATE `$wpdb->postmeta` SET meta_key = %s WHERE meta_key = %s",
					sanitize_key( $args[1] ),
					sanitize_key( $args[0] )
				)
			);
			WP_CLI::log( "Updated {$result} rows." );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
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
