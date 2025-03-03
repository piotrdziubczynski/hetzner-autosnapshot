# Hetzner auto-snapshot

If you use Hetzner Cloud as your hosting, you probably need to create a backup. Hetzner Cloud provides a backup service, but you cannot move them to another server or project. And that is why this script was created 😊

Snapshot is a more flexible unit in the Hetzner environment and provides a backup as well.

## How to use

1. Copy the repository to your favorite server.
    ```bash
    git clone git@github.com:piotrdziubczynski/hetzner-autosnapshot.git .
    ```

2. Rename the `.env.dist` file to `.env`.

3. Enter the correct values for the `API_TOKEN` and `SERVERS_LIST` environment variables. These two are required. The other variables are optional and have default values.

4. Run:
    ```bash
    composer install
    ```

5. Set up two cron jobs:
   - daily: `... /private/create-snapshot.php`
   - daily, but 15-20 minutes later than create: `... /private/delete-snapshot.php`

   For example, your "create" cron starts at 2:00 AM, so you should set the "delete" cron at 2:15 AM.

### More information

You can also change the default value of the prefix of future images by changing the `IMAGE_PREFIX` variable.

⚠️ Note: The script will not affect your manually created backups if you do not use the prefix from this script.

⚠️ Note: If you change the prefix during cron, the script will not delete old snapshots. You will have to delete them manually.

By default, snapshots are created "daily" and deleted "weekly". Therefore, the server should have at least seven snapshots.

⚠️ Note: If you want to change this behavior, look at this file: `... /src/Core/Enum/Frequency.php`, select the option and enter the correct value for the `FREQUENCY_BACKUP` or `FREQUENCY_DELETING` variable.

Also, the seven newest snapshots will not be deleted by default. If your "create" cron job stops working, this is a safety measure. To change this, change the value of `IMAGE_BACKUP_MINIMUM`.

The `IMAGE_DELETE_UNASSIGN` variable deletes non-existent server images. By default, it is `1` (true), because we don't want to pay for this disk space. However, you can change it to `0` (false).
