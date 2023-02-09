# Drupal Paketo Scaffold
This package provides [scaffold files](scaffold/) for building a Drupal application container image with the [Paketo Buildpacks](https://paketo.io/). The scaffold files are installed into the [needed locations](composer.json#L14-L17) during a `composer install` operation by the [Drupal Composer Scaffold](https://github.com/drupal/core-composer-scaffold) plugin that is included by default within [drupal/recommended-project](https://github.com/drupal/recommended-project).

There are [several benefits to building container images with buildpacks](https://tanzu.vmware.com/developer/blog/understanding-the-differences-between-dockerfile-and-cloud-native-buildpacks/), including how they simplify the work of combining multiple infrastructure services (such as Apache HTTPD and PHP-FPM) into the same container image, for cases where that's preferred over separating the containers and connecting them with Docker Compose.

## Usage

### Step 1: Create a Drupal application as you normally would:
```
# Create the project. This adds only the composer.json and composer.lock files
# to the "my-app" directory. If you want to run the Drupal application locally
# before building the container image, you may omit the "--no-install" option,
# but this example uses --no-install to emulate what you would end up
# committing to your version control repository.
composer create-project drupal/recommended-project my-app --no-install
cd my_app

# Add whatever other contrib or custom Drupal modules, themes, etc. you want,
# also using the --no-install option (or not) for each `composer require`.
...

# Either before, after, or in between the above, also add the Drupal Paketo
# Scaffold package. The first command below won't be needed once this package
# is published on either Packagist or drupal.org, but I'm not ready to do
# that yet, and I might decide to change the namespace or name of the
# package before doing it.
composer config repositories.drupal-paketo-scaffold vcs https://github.com/effulgentsia/drupal-paketo-scaffold
composer config --merge extra.drupal-scaffold.allowed-packages --json '["effulgentsia/drupal-paketo-scaffold"]'
composer require effulgentsia/drupal-paketo-scaffold:@dev --no-install
```

### Step 2: Create a container image from the application
Assuming you already have [Pack](https://buildpacks.io/docs/tools/pack/) installed and [Docker](https://www.docker.com/) running, the following is all that's needed to build the container image:
```
# The project.toml file is needed. If you ran the earlier steps without the
# --no-install option, then you already have it. If not, you can get it
# either by running `composer install` (but then you get everything else
# that that installs), or if you want to keep your workspace clean, you can
# just do:
curl -O https://raw.githubusercontent.com/effulgentsia/drupal-paketo-scaffold/main/scaffold/project.toml

# As long as this directory contains project.toml, composer.json, and
# composer.lock, we can build the image:
pack build my-app --clear-cache
```

### Step 3: Run the container:
```
docker run --rm -p 8000:8080 --env DRUPAL_HASH_SALT=$(openssl rand -base64 48) my-app
```

Now you can navigate your browser to http://localhost:8000 to install your desired Drupal profile and use your site.

## Retaining data across container restarts
With the above example, the Drupal SQLite database and the directories for uploaded files (including public, private, temp, and config_sync) are in the container and are therefore ephemeral, so all Drupal configuration and content gets destroyed when the container terminates. You can [attach a volume to the /workspace/.volume directory](examples/2-attach-volume) in order to make that data persistent across container restarts.

## Using a different database than SQLite
If you prefer, you can [use MySQL](examples/3-mysql) or another database instead of SQLite.

## Warnings
- This package is pre-release code and is not well tested. Don't use it for anything important.
