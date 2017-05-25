<?php

namespace Drupal\presto\Installer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures Presto's additional functionality.
 *
 * @package Drupal\presto\Installer\Form
 */
class PrestoConfigureForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'presto_configure_presto';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Configure Presto functionality');

    // Clear any module success messages.
    drupal_get_messages('status');

    $form['ecommerce'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('eCommerce'),
      '#collapsible' => FALSE,
    ];

    // Only enable commerce if this profile was installed via Composer
    // (which is when the below interface will exist).
    $enableCommerce = FALSE;
    if (interface_exists('CommerceGuys\Intl\Currency\CurrencyInterface')) {
      $enableCommerce = TRUE;
    }

    if (!$enableCommerce) {
      $disabledMsg = $this->t('<p><strong>Not supported.</strong></p><p>Unfortunately, eCommerce is only supported if you install Presto via Composer. <a href=":url">See the README for more information on installing via Composer.</a></p>', [
        ':url' => 'https://github.com/Sitback/presto#installing-presto',
      ]);
      $form['ecommerce']['disabled_info'] = [
        '#type' => 'markup',
        '#markup' => $disabledMsg,
      ];
    }

    $form['ecommerce']['enable_ecommerce'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable eCommerce'),
      '#description' => $this->t(
        'Enables Drupal Commerce and some sane defaults to help you kickstart your eCommerce site.'
      ),
      '#disabled' => !$enableCommerce,
      '#default_value' => $enableCommerce,
    ];

    $form['ecommerce']['ecommerce_install_demo_content'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Install Demo Content'),
      '#description' => $this->t(
        'Creates a few demo products to help you test your new eCommerce site.'
      ),
      '#disabled' => !$enableCommerce,
      '#default_value' => $enableCommerce,
      '#states' => [
        'visible' => [
          'input[name="enable_ecommerce"]' => [
            'checked' => TRUE,
          ],
        ],
        'unchecked' => [
          'input[name="enable_ecommerce"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save and continue'),
      '#button_type' => 'primary',
      '#submit' => ['::submitForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $buildInfo = $form_state->getBuildInfo();

    $install_state = $buildInfo['args'];

    // Tell the 'presto_apply_configuration' install task that it should go
    // ahead and enable eCommerce if required.
    $install_state[0]['presto_ecommerce_enabled'] = (bool) $form_state->getValue(
      'enable_ecommerce'
    );
    $install_state[0]['presto_ecommerce_install_demo_content'] = (bool) $form_state->getValue(
      'ecommerce_install_demo_content'
    );
    $install_state[0]['form_state_values'] = $this->value(
      $form_state->getValues()
    );

    $buildInfo['args'] = $install_state;

    $form_state->setBuildInfo($buildInfo);
  }

  /**
   * Converts a variable reference to a value.
   *
   * @param mixed $reference
   *   Reference to convert.
   *
   * @return mixed
   *   Value.
   */
  private function value(&$reference) {
    return $reference;
  }

}
