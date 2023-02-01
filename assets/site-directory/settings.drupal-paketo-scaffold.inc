<?php

$drupal_paketo_scaffold ??= [];
$drupal_paketo_scaffold += [
  'base_directory' => '/workspace/',
  'database_directory' => '/workspace/_data/database/',
  'files_directory' => '/workspace/_data/files/',
];

$databases['default']['default'] ??= [
  'database' => $drupal_paketo_scaffold['database_directory'] . '.ht.sqlite',
  'prefix' => '',
  'namespace' => 'Drupal\\sqlite\\Driver\\Database\\sqlite',
  'driver' => 'sqlite',
  'autoload' => 'core/modules/sqlite/src/Driver/Database/sqlite/',
];

$settings['hash_salt'] ??= file_get_contents($drupal_paketo_scaffold['base_directory'] . '.drupal/.ht.hash_salt');
$settings['config_sync_directory'] ??= $drupal_paketo_scaffold['files_directory'] . 'config_sync';
$settings['file_private_path'] ??= $drupal_paketo_scaffold['files_directory'] . 'private';
$settings['file_temp_path'] ??= $drupal_paketo_scaffold['files_directory'] . 'temp';
