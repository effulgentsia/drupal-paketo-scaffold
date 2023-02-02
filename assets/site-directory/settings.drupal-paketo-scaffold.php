<?php

// Determine the base directory, database directory, and files directory.
//
// The defaults are:
//   base: /workspace
//   database: /workspace/.drupal/storage/database
//   files: /workspace/.drupal/storage/files
//
// An optional file in /workspace/.drupal/storage.php can override the
// database and file directories.
$drupal_paketo_scaffold ??= [];
$drupal_paketo_scaffold += [
  'base_directory' => '/workspace',
];
if (file_exists($drupal_paketo_scaffold['base_directory'] . '/.drupal/storage.php')) {
  $drupal_paketo_scaffold += (require $drupal_paketo_scaffold['base_directory'] . '/.drupal/storage.php')['run'];
}
$drupal_paketo_scaffold += [
  'database_directory' => $drupal_paketo_scaffold['base_directory'] . '/.drupal/storage/database',
  'files_directory' => $drupal_paketo_scaffold['base_directory'] . '/.drupal/storage/files',
];

// Add a default datatabase connection if one isn't already defined.
$databases['default']['default'] ??= [
  'driver' => 'sqlite',
  // The name of the SQLite file within the directory doesn't matter, so use
  // the same name as Drupal's web installer's default. This name provides
  // extra security for when the file would otherwise be web accessible, but
  // that's irrelevant when the database directory is outside of the web root.
  'database' => $drupal_paketo_scaffold['database_directory'] . '/.ht.sqlite',
];

// Add defaults for key settings if they're not already defined.
$settings['hash_salt'] ??= require $drupal_paketo_scaffold['base_directory'] . '/.drupal/secrets/hash_salt.php';
$settings['config_sync_directory'] ??= $drupal_paketo_scaffold['files_directory'] . '/config_sync';
$settings['file_private_path'] ??= $drupal_paketo_scaffold['files_directory'] . '/private';
$settings['file_temp_path'] ??= $drupal_paketo_scaffold['files_directory'] . '/temp';
