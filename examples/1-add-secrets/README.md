To use Docker's secrets manager (for things like the Drupal hash salt, or in later examples for the database password), you can copy the [docker-compose.yml](docker-compose.yml) file that's in this directory to your project directory and then run:
```
docker swarm init
openssl rand -base64 55 | docker secret create drupal-hash-salt -
docker stack deploy -c docker-compose.yml my-stack
```

As with [the previous example](../0-default), you can now navigate your browser window to http://localhost:8000. The Drupal database and uploaded files are still ephemeral. To view logs that are output by the container, run:
```
docker service logs my-stack_app
```

To stop the container, run:
```
docker stack rm my-stack
```

With this in place, see [the next example](../2-attach-volume) for how to attach a volume for persistent storage.
