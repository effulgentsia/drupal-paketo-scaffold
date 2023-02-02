# Drupal Paketo Scaffold
This Composer plugin implements scaffolding for building a Drupal application container image with the [Paketo Buildpacks](https://paketo.io/). There are [several benefits to building container images with buildpacks](https://tanzu.vmware.com/developer/blog/understanding-the-differences-between-dockerfile-and-cloud-native-buildpacks/), including how they simplify the work of combining multiple infrastructure services (such as Apache HTTPD and PHP-FPM) into the same container image, for cases where that's preferred over separating the containers and connecting them with Docker Compose.

## Usage
Assuming you already have [Pack](https://buildpacks.io/docs/tools/pack/) installed and [Docker](https://www.docker.com/) running, the following is all that's needed to build a Drupal container image:
```
# Create the project. This adds only the composer.json and composer.lock files
# to the "my_app" directory. If you want to run the Drupal application locally
# before building the container image, you may omit the "--no-install" option,
# but that is not needed.
composer create-project drupal/recommended-project my_app --no-install
cd my_app

# Add the Drupal Paketo Scaffold plugin. The first command won't be needed once
# this package is published on either Packagist or drupal.org, but I'm not
# ready to do that yet. I might decide to change the namespace or name of the
# package before doing that.
composer config repositories.drupal-paketo-scaffold vcs https://github.com/effulgentsia/drupal-paketo-scaffold
composer config allow-plugins.effulgentsia/drupal-paketo-scaffold true
composer require effulgentsia/drupal-paketo-scaffold:@dev --no-install

# Add whatever other contrib or custom Drupal modules, themes, etc. you want.
...

# Create the container image using the Paketo builder.
pack build my_app_image --builder=paketobuildpacks/builder-jammy-full --env BP_PHP_SERVER=httpd --env BP_PHP_WEB_DIR=web --env BPE_DEFAULT_PORT=8080 --env BP_COMPOSER_INSTALL_OPTIONS="--ignore-platform-req=ext-gd --ignore-platform-req=ext-pdo" --clear-cache

# Run the container.
docker run --rm -p 8000:8080 my_app_image

# Navigate your browser to http://localhost:8000 and install your Drupal site.
```

## TODO
Add documentation explaining how this plugin works and how to customize its behavior.

## Warning
This package is pre-release code and is not well tested. Don't use it for anything serious.
