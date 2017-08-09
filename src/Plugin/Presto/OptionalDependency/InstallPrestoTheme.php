<?php

namespace Drupal\presto\Plugin\Presto\OptionalDependency;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\presto\Installer\DependencyTypes;

/**
 * Installs presto theme if selected..
 *
 * @PrestoOptionalDependency(
 *     id = "install_presto_theme",
 *     label = @Translation("Install Presto Theme"),
 *     weight = 0
 * )
 */
class InstallPrestoTheme extends AbstractOptionalDependency {

  const THEME_NAME = 'presto_theme';

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      // Theme is installed by default.
      static::THEME_NAME => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldInstall(array $installState) {
    return $this->configuration[static::THEME_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getInstallOperations() {
    return [
      [
        [static::class, 'installDependency'],
        [
          static::THEME_NAME,
          DependencyTypes::THEME,
        ],
      ],
      [
        [static::class, 'definePrestoThemeAsDefault'],
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
    $form['presto_theme'] = [
      '#type' => 'checkbox',
      '#title' => t('Install Presto Theme'),
      '#description' => t(
        'Install and set as default the Presto Theme.'
      ),
      '#default_value' => $this->configuration[static::THEME_NAME],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(
    array &$form,
    FormStateInterface $form_state
  ) {
    $this->configuration[static::THEME_NAME] = (bool) $form_state->getValue(
      'presto_theme'
    );
  }

  /**
   * Define Presto Theme as Default. Used by the batch during install process.
   *
   * @throws \Drupal\Core\Config\ConfigValueException
   */
  public static function definePrestoThemeAsDefault() {
    // Set presto_theme as default.
    Drupal::configFactory()
      ->getEditable('system.theme')
      ->set('default', 'presto_theme')
      ->save();

    // Set seven as admin theme.
    Drupal::configFactory()
      ->getEditable('system.theme')
      ->set('admin', 'seven')
      ->save();
  }

}
