You can run a container from the built image, simply with:
```
docker run --rm -p 8000:8080 --env DRUPAL_HASH_SALT=$(openssl rand -base64 55) my-app
```

That will run the container until you press Ctrl+C. While running, you can navigate your browser window to http://localhost:8000 to install your desired Drupal profile and then run your site. However, in this mode, everything is ephemeral: your database and uploaded files will be destroyed when the container terminates (when you press Ctrl+C). You can attach a volume to the container to make the database and uploaded files persist across container executions, but if you're going to do that, you're going to also want to persist the hash salt (the above generates a new random one each time the container is run), and to do so securely. To do that, see the next example.
