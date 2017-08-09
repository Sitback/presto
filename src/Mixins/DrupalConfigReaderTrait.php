<?php

namespace Drupal\presto\Mixins;

use Drupal;
use Drupal\Core\Config\FileStorage;

/**
 * Provides helpers to re-read config from a YML file into the database.
 *
 * @package Drupal\presto\Mixins
 */
trait DrupalConfigReaderTrait {

  /**
   * Re-read config from file into active storage.
   *
   * @param string $path
   *   Config path.
   * @param string $file
   *   Config file name.
   *
   * @return bool
   *   TRUE if successful, FALSE otherwise.
   *
   * @throws \Drupal\Core\Config\StorageException
   *   If a config write failure occurs.
   * @throws \Drupal\Core\Config\UnsupportedDataTypeConfigException
   *   If a config read failur occurs.
   */
  protected static function readConfig($path, $file) {
    $source = new FileStorage($path);

    // Re-read checkout flow from the export config file.
    // This should be safe enough as this only runs within a site install
    // context.
    /** @var \Drupal\Core\Config\StorageInterface $configStorage */
    $configStorage = Drupal::service('config.storage');
    return $configStorage->write(
      $file,
      $source->read($file)
    );
  }

}
