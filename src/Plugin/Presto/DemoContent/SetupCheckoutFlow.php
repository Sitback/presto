<?php

namespace Drupal\presto\Plugin\Presto\DemoContent;

use Drupal\presto\Mixins\DrupalConfigReaderTrait;

/**
 * Sets up the checkout flow.
 *
 * @PrestoDemoContent(
 *     id = "setup_checkout_flow",
 *     type = \Drupal\presto\Installer\DemoContentTypes::ECOMMERCE,
 *     label = @Translation("Setup checkout flow"),
 *     weight = 4
 * )
 *
 * @package Drupal\presto\Plugin\Presto\DemoContent
 */
class SetupCheckoutFlow extends AbstractDemoContent {

  use DrupalConfigReaderTrait;

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Config\UnsupportedDataTypeConfigException
   * @throws \Drupal\Core\Config\StorageException
   */
  public function createContent() {
    $modulePath = drupal_get_path('module', 'presto_commerce');
    $configPath = "{$modulePath}/config/optional";
    static::readConfig(
      $configPath,
      'commerce_checkout.commerce_checkout_flow.default'
    );
  }

}
