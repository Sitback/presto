<?php

namespace Drupal\presto\Plugin\Presto\OptionalDependency;

use Drupal\block\Entity\Block;
use Drupal\Core\Form\FormStateInterface;

/**
 * Installs Presto Theme Commerce Blocks if possible.
 *
 * @PrestoOptionalDependency(
 *     id = "install_presto_theme_commerce_blocks",
 *     label = @Translation("Install Presto Theme Commerce Blocks"),
 *     weight = 1
 * )
 */
class InstallPrestoThemeCommerceBlocks extends AbstractOptionalDependency {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldInstall(array $installState) {
    $installTheme = $installState['optional_dependencies']['install_presto_theme']['presto_theme'];
    $installCommerce = !empty($installState['presto_ecommerce_enabled']);
    return $installTheme && $installCommerce;
  }

  /**
   * Get any required Batch API install operations for this dependency.
   *
   * @return array
   *   Batch operation definitions.
   */
  public function getInstallOperations() {
    return [
      [
        [static::class, 'createBlocks'],
        [],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(
    array $form,
    FormStateInterface $form_state
  ) {
    // No configuration required for this dependency.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(
    array &$form,
    FormStateInterface $form_state
  ) {
    // Nothing to do, we don't have a form.
  }

  /**
   * Create block.
   *
   * We do a manual entity create as for some reason it won't import via config.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function createBlocks() {
    $block = Block::create([
      'id' => 'presto_theme_views_block__presto_product_listing_listing_block',
      'status' => TRUE,
      'plugin' => 'views_block:presto_product_listing-listing_block',
      'weight' => 10,
      'theme' => 'presto_theme',
      'region' => 'content',
      'visibility' => [
        'request_path' => [
          'id' => 'request_path',
          'pages' => '/products',
          'negate' => FALSE,
          'context_mapping' => [],
        ],
      ],
    ]);
    $block->save();

    $block = Block::create([
      'id' => 'presto_theme_cart',
      'status' => TRUE,
      'plugin' => 'commerce_cart',
      'weight' => 30,
      'theme' => 'presto_theme',
      'region' => 'navigation_collapsible',
      'visibility' => [],
      'settings' => [
        'id' => 'commerce_cart',
        'label' => 'Cart',
        'provider' => 'commerce_cart',
        'label_display' => '0',
        'dropdown' => 'false',
      ],
    ]);
    $block->save();
  }

}
