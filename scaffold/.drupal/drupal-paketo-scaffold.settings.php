<?php

/**
 * Do all of this file's work inside of an immediately invoked anonymous
 * function in order to create a local scope for variables. The only
 * side effect we want to have on the file that includes this one is to
 * add to $databases and $settings.
 */
(function () use ($databases, $settings) {

  $app_root = $_SERVER['CNB_APP_DIR'] ?? '/workspace';

  // It is more secure for values of secrets to not be in environment variables.
  // Docker's secrets manager passes secrets via a file, so that the environment
  // variable is just the name of the file rather than its contents. The
  // convention for this pattern is to correspondingly append _FILE to the name
  // of the environment variable. If that doesn't exist, we fall back to the
  // less secure approach of getting the secret directly from the environment
  // variable for cases where Docker's secrets manager or a similar one can't be
  // used.
  $getSecret = function ($name) {
    return isset($_SERVER[$name . '_FILE']) ? file_get_contents($_SERVER[$name . '_FILE']) : $_SERVER[$name];
  };

  // Add the default datatabase connection if it's not already defined.
  if (!isset($databases['default']['default'])) {
    $db_driver = $_SERVER['DRUPAL_DB_DRIVER'] ?? 'sqlite';
    switch ($db_driver) {
      case 'sqlite':
        $databases['default']['default'] = [
          'driver' => $db_driver,
          // The name of the SQLite file within the directory doesn't matter, so use
          // the same name as Drupal's web installer's default.
          'database' => $app_root . '/' . $_SERVER['DRUPAL_DB_DIR'] . '/.ht.sqlite',
        ];
        break;

      case 'mysql':
      case 'pgsql':
        $databases['default']['default'] = [
          'driver' => $db_driver,
          'host' => $getSecret('DRUPAL_DB_HOST'),
          'username' => $getSecret('DRUPAL_DB_USER'),
          'password' => $getSecret('DRUPAL_DB_PASSWORD'),
          'database' => $getSecret('DRUPAL_DB_NAME']),
        ];
        break;
    }
  }

  // Add the hash salt.
  $settings['hash_salt'] ??= $getSecret('DRUPAL_HASH_SALT');

  // Add the non-public files directories.
  // - $_SERVER['DRUPAL_FILES_DIR'] should be outside of the web root.
  // - The public files directory is not defined here, because we retain the
  //   default of sites/default/files so that it is web accessible.
  $files_root = $app_root . '/' . $_SERVER['DRUPAL_FILES_DIR'];
  $settings['config_sync_directory'] ??= $files_root . '/config_sync';
  $settings['file_private_path'] ??= $files_root . '/private';
  $settings['file_temp_path'] ??= $files_root . '/temp';

})();
