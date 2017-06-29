<?php

namespace Drupal\presto\Plugin\Presto\DemoContent;

use Drupal;
use Drupal\Core\Config\FileStorage;

/**
 * Sets up the checkout flow.
 *
 * @PrestoDemoContent(
 *     id = "setup_product_form_display",
 *     type = \Drupal\presto\Installer\DemoContentTypes::ECOMMERCE,
 *     label = @Translation("Setup product form display"),
 *     weight = 12
 * )
 *
 * @package Drupal\presto\Plugin\Presto\DemoContent
 */
class SetupProductFormDisplay extends AbstractDemoContent {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Config\UnsupportedDataTypeConfigException
   */
  public function createContent() {
    $modulePath = drupal_get_path('module', 'presto_commerce');
    $configPath = "{$modulePath}/config/optional";

    $source = new FileStorage($configPath);

    // Re-read checkout form display from the export config file.
    // This should be safe enough as this only runs within a site install
    // context.
    $configStorage = Drupal::service('config.storage');
    $configStorage->write(
      'core.entity_form_display.commerce_product.book.default',
      $source->read('core.entity_form_display.commerce_product.book.default')
    );
    $configStorage->write(
      'core.entity_form_display.commerce_product.ebook.default',
      $source->read('core.entity_form_display.commerce_product.ebook.default')
    );
  }

}
