# cata-cli
WP CLI commands, initially for post meta

## Commands

In all commands, appending `--dry_run=true` will perform a dry run.

In all commands, appending `--post_type=<post_type>` will limit the scope to a single post type (e.g. `--post_type=post`). Defaults to `any`, which affects all post types.

### Update Post Meta Key

`wp cata update_post_meta_key old_key new_key`

Updates all instances of the old key to the new key.

Limit to a single post type:

`wp cata update_post_meta_key old_key new_key --post_type=post`

### Delete Post Meta

`wp cata delete_post_meta key`

Deletes all instance of the key.

Limit to a single post type:

`wp cata delete_post_meta key --post_type=post`
