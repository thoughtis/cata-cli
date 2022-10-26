# cata-cli
WP CLI commands, initially for post meta

## Commands

In all commands, appending `--dry_run=true` will perform a dry run.

### Update Post Meta Key

`wp cata update_post_meta_key old_key new_key`

Updates all instances of the old key to the new key.

### Delete Post Meta

`wp cata delete_post_meta key`

Deletes all instance of the key.
