<?php

namespace Drupal\presto\Plugin\Presto\OptionalDependency;

use Drupal\Core\Form\FormStateInterface;

/**
 * Installs Presto Commerce Currency if possible.
 *
 * @PrestoOptionalDependency(
 *     id = "install_presto_commerce_currency",
 *     label = @Translation("Install Presto Commerce Currency"),
 *     weight = 1
 * )
 */
class InstallPrestoCommerceCurrency extends AbstractOptionalDependency {

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
    return !empty($installState['presto_ecommerce_enabled']);
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
        [static::class, 'createCurrency'],
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
   * Create currency.
   *
   * We do a manual currency create if the site country is not Australia as
   * the shop is setup for Australia.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function createCurrency() {
    // If default country is Australia do not import the AUD Dollar.
    $default_country = \Drupal::config('system.date')->get('country.default');
    if ($default_country !== 'AU') {
      $currency_importer = \Drupal::service('commerce_price.currency_importer');
      $currency_importer->importByCountry('AU');
    }
  }

}
