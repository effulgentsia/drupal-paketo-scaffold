This adds on to the [previous example](../2-attach-volume/) by switching from SQLite to MySQL (or more precisely, to MariaDB). Unlike SQLite, MySQL/MariaDB runs in a separate service, which has been added to this example's [docker-compose.yml](docker-compose.yml). This adds additional secrets (the database passwords, which don't need to be as long as hash salts), so the commands to start this are:
```
docker swarm init
openssl rand -base64 55 | docker secret create drupal-hash-salt -
openssl rand -base64 32 | docker secret create db-root-password -
openssl rand -base64 32 | docker secret create drupal-db-password -
docker stack deploy -c docker-compose.yml my-stack
```
