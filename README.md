# Cata CLI

WP-CLI commands to support sites using the Cata parent theme. Currently focused on bulk post meta operations.

## Requirements

- WordPress with [WP-CLI](https://wp-cli.org/) available
- PHP 7.4+

Commands are only registered when running under WP-CLI.

## Installation

Place this directory in `wp-content/plugins/` and activate the plugin.

## Common options

Both commands accept:

- `--dry_run=true` — report matched rows (and show the preview table for deletes) without modifying anything. **Note:** the value is required; a bare `--dry_run` flag is *not* recognized and the command will run for real.
- `--post_type=<post_type>` — limit the operation to a single post type (e.g. `--post_type=post`). Defaults to `any`, which skips the join against the posts table entirely and affects **all** postmeta rows — including meta belonging to revisions and orphaned meta whose parent post no longer exists. An invalid post type exits without making changes.

Meta keys are run through `sanitize_key()`, so they are lowercased and limited to `a-z`, `0-9`, `_`, and `-`. Keys containing other characters cannot be targeted.

These commands modify the database directly. If you use a persistent object cache, flush it after a real run: `wp cache flush`.

## Commands

### Update Post Meta Key

    wp cata update_post_meta_key <old_key> <new_key> [--post_type=<post_type>] [--dry_run=true]

Renames all instances of `<old_key>` to `<new_key>`, reporting the number of rows found and updated.

### Delete Post Meta

    wp cata delete_post_meta <key> [--post_type=<post_type>] [--dry_run=true]

Deletes all instances of `<key>`. Before deleting (including on dry runs), prints the number of matching rows and a table of the first 10 matches for reference.

## License

GPL v3 or later. See [LICENSE](LICENSE).
