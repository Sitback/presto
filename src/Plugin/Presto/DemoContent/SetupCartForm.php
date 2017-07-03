<?php

namespace Drupal\presto\Plugin\Presto\DemoContent;

use Drupal;
use Drupal\Core\Config\FileStorage;

/**
 * Sets up the Views Cart Form.
 *
 * @PrestoDemoContent(
 *     id = "setup_views_cart_form",
 *     type = \Drupal\presto\Installer\DemoContentTypes::ECOMMERCE,
 *     label = @Translation("Setup views cart form"),
 *     weight = 11
 * )
 *
 * @package Drupal\presto\Plugin\Presto\DemoContent
 */
class SetupCartForm extends AbstractDemoContent {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Config\UnsupportedDataTypeConfigException
   */
  public function createContent() {
    $modulePath = drupal_get_path('module', 'presto_commerce');
    $configPath = "{$modulePath}/config/optional";

    $source = new FileStorage($configPath);

    // Re-read Views Cart Form from the export config file.
    // This should be safe enough as this only runs within a site install
    // context.
    $configStorage = Drupal::service('config.storage');
    $configStorage->write(
      'views.view.commerce_cart_form',
      $source->read('views.view.commerce_cart_form')
    );
  }

}
