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
    $this->io->write('Paketo scaffold');
    $base_directory = '.';
    $database_directory = './_data/database';
    $files_directory = './_data/files';
    $site_directory = './web/sites/default';

    # PHP configuration.
    if (!file_exists($base_directory . '/.php.ini.d')) {
      mkdir($base_directory . '/.php.ini.d');
    }
    if (!file_exists($base_directory . '/.php.ini.d/drupal-paketo-scaffold.ini')) {
      copy(__DIR__ . '/../assets/base-directory/.php.ini.d/drupal-paketo-scaffold.ini', $base_directory . '/.php.ini.d/drupal-paketo-scaffold.ini');
    }

    # Drupal hash salt.
    if (!file_exists($base_directory . '/.drupal')) {
      mkdir($base_directory . '/.drupal');
    }
    if (!file_exists($base_directory . '/.drupal/.ht.hash_salt')) {
      $hash_salt = base64_encode(random_bytes(55));
      file_put_contents($base_directory . '/.drupal/.ht.hash_salt', $hash_salt);
    }

    # The default value for mkdir()'s $permissions parameter. It needs to be
    # passed explicitly when setting $recurse to true. mkdir() internally also
    # applies the umask filter, so the directories do not actually receive this
    # broad permission.
    $default_mkdir_permission = 0777;

    # Database and files directories. These need to be writable by the runtime
    # user, which is different than, but in the same group as, the build time
    # user.
    if (!file_exists($database_directory)) {
      mkdir($database_directory, $default_mkdir_permission, true);
      chmod($database_directory, "g+w");
    }
    foreach (['public', 'private', 'temp', 'config_sync'] as $type) {
      if (!file_exists($files_directory)) {
        mkdir($files_directory . '/' . $type, $default_mkdir_permission, true);
        chmod($files_directory . '/' . $type, "g+w");
      }
    }

    # Additions to the site (web/sites/default) directory.
    if (!file_exists($site_directory . '/settings.drupal-paketo-scaffold.inc')) {
      copy(__DIR__ . '/../assets/site-directory/settings.drupal-paketo-scaffold.inc', $site_directory . '/settings.drupal-paketo-scaffold.inc');
    }
    if (!file_exists($site_directory . '/settings.php')) {
      file_put_contents($site_directory . '/settings.php', '<' . '?php require __DIR__ . "/settings.drupal-paketo-scaffold.inc";');
    }
    if (!file_exists($site_directory . '/files')) {
      symlink(realpath($files_directory . '/public'), $site_directory . '/files');
    }
  }

}
