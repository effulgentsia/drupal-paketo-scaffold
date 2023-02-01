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
      'post-drupal-scaffold-cmd' => 'scaffold',
    ];
  }

  /**
   * Add the scaffold files.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event.
   */
  public function scaffold(Event $event) {
  }

}
