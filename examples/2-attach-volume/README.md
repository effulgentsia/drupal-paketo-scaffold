The commands to run are the same as in the [previous example](../1-add-secrets/). This example just includes a couple more entries in the [docker-compose.yml](docker-compose.yml) that attach a persistent volume to the /workspace/.volume directory, where the container stores the Drupal SQLite database and uploaded files. Now you can stop and restart the container whenever you want, and the database and uploaded files are still there from before.

If you'd prefer to use MySQL instead of SQLite, see the [next example](../3-mysql/).
