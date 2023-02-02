<?php

namespace Effulgentsia\DrupalPaketoScaffold;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;

/**
 * Composer plugin for adding scaffold files.
 *
 * @internal
 */
class Plugin implements PluginInterface, EventSubscriberInterface {

  private const PHP_START = '<' . '?php' . "\n";

  /**
   * The Composer service.
   *
   * @var \Composer\Composer
   */
  protected $composer;

  /**
   * Composer's I/O service.
   *
   * @var \Composer\IO\IOInterface
   */
  protected $io;

  /**
   * {@inheritdoc}
   */
  public function activate(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  /**
   * {@inheritdoc}
   */
  public function deactivate(Composer $composer, IOInterface $io) {
  }

  /**
   * {@inheritdoc}
   */
  public function uninstall(Composer $composer, IOInterface $io) {
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      // Run after drupal/core-composer-scaffold.
      // @todo Change to use 'post-drupal-scaffold-cmd' event instead.
      'post-install-cmd' => ['scaffold', -1],
    ];
  }

  /**
   * Add the scaffold files.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event.
   */
  public function scaffold(Event $event) {
    // Within the container, the base directory is /workspace, but
    // during build time, reference it as '.', so that
    // `composer install` can also be run on the host computer.
    $base_directory = '.';

    // Get the database and files directories. Allow the defaults to be
    // overridden by an optional ./.drupal/storage.php.
    $storage_directories = file_exists($base_directory . '/.drupal/storage.php') ? (require $base_directory . '/.drupal/storage.php') : ['build' => [], 'run' => []];
    $database_directory = $storage_directories['build']['database_directory'] ?? $base_directory . '/.drupal/storage/database';
    $files_directory = $storage_directories['build']['files_directory'] ?? $base_directory . '/.drupal/storage/files';
    $runtime_files_directory = $storage_directories['run']['files_directory'] ?? '/workspace/.drupal/storage/files';

    // Get the site directory (e.g., ./web/sites/default).
    $extra = $this->composer->getPackage()->getExtra();
    $webroot = $extra['drupal-scaffold']['locations']['web-root'] ?? 'web';
    $site_directory = $base_directory . '/' . rtrim($webroot, '/') . '/sites/default';

    # PHP configuration.
    if (!file_exists($base_directory . '/.php.ini.d')) {
      mkdir($base_directory . '/.php.ini.d');
    }
    foreach (['drupal-paketo-scaffold.ini', 'drupal-paketo-scaffold-database.ini'] as $file) { 
      if (!file_exists($base_directory . '/.php.ini.d/' . $file)) {
        copy(__DIR__ . '/../assets/base-directory/.php.ini.d/' . $file, $base_directory . '/.php.ini.d/' . $file);
      }
    }

    # Drupal hash salt.
    if (!file_exists($base_directory . '/.drupal')) {
      mkdir($base_directory . '/.drupal');
    }
    if (!file_exists($base_directory . '/.drupal/secrets')) {
      mkdir($base_directory . '/.drupal/secrets');
      chmod($base_directory . '/.drupal/secrets', 0750);
    }
    if (!file_exists($base_directory . '/.drupal/secrets/hash_salt.php')) {
      // Generate a hash salt the same way as Drupal's web installer does,
      // except it doesn't need to be made URL-safe.
      $hash_salt = base64_encode(random_bytes(55));
      file_put_contents($base_directory . '/.drupal/secrets/hash_salt.php', self::PHP_START . "return '" . $hash_salt . "';");
    }

    // Database and files directories. These need to be writable by the runtime
    // user, which is different than, but in the same group as, the build time
    // user.
    $writeable_directory_permissions = 0775;
    if (!file_exists($database_directory)) {
      mkdir($database_directory, $writeable_directory_permissions, true);
      // mkdir() applies the umask filter, so chmod() is needed as well.
      chmod($database_directory, $writeable_directory_permissions);
    }
    foreach (['public', 'private', 'temp', 'config_sync'] as $type) {
      if (!file_exists($files_directory . '/' . $type)) {
        mkdir($files_directory . '/' . $type, $writeable_directory_permissions, true);
        // mkdir() applies the umask filter, so chmod() is needed as well.
        chmod($files_directory . '/' . $type, $writeable_directory_permissions);
      }
    }

    // Additions to the site (web/sites/default) directory.
    if (!file_exists($site_directory . '/settings.drupal-paketo-scaffold.inc')) {
      copy(__DIR__ . '/../assets/site-directory/settings.drupal-paketo-scaffold.inc', $site_directory . '/settings.drupal-paketo-scaffold.inc');
    }
    if (!file_exists($site_directory . '/settings.php')) {
      file_put_contents($site_directory . '/settings.php', '<' . '?php require __DIR__ . \'/settings.drupal-paketo-scaffold.inc\';');
    }
    if (!file_exists($site_directory . '/files')) {
      symlink(realpath($files_directory . '/public'), $site_directory . '/files');
    }
  }

}
