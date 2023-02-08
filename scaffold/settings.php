<?php

$drupal_paketo_scaffold['app-root'] = $_SERVER['CNB_APP_DIR'] ?? '/workspace';
require $drupal_paketo_scaffold['app-root'] . '/.drupal/drupal-paketo-scaffold.settings.php';
if (isset($_SERVER['DRUPAL_ADDITIONAL_SETTINGS_FILE'])) {
  @include $drupal_paketo_scaffold['app-root'] . '/' . $_SERVER['DRUPAL_ADDITIONAL_SETTINGS_FILE'];
}
