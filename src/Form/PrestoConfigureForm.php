<?php

namespace Drupal\presto\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Render\Element;
use Drupal\presto\Installer\OptionalDependencies\OptionalDependenciesInstaller;
use Drupal\presto\Installer\OptionalDependencies\OptionalDependencyManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures Presto's additional functionality.
 *
 * @package Drupal\presto\Form
 */
class PrestoConfigureForm extends FormBase {

  /**
   * The optional dependency manager.
   *
   * @var \Drupal\presto\Installer\OptionalDependencies\OptionalDependencyManager
   */
  private $optionalDependencyManager;

  /**
   * PrestoConfigureForm constructor.
   *
   * @param \Drupal\presto\Installer\OptionalDependencies\OptionalDependencyManager $manager
   *   The optional dependency manager.
   */
  public function __construct(OptionalDependencyManager $manager) {
    $this->optionalDependencyManager = $manager;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.presto.optional_dependencies')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'presto_configure_presto';
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Configure Presto functionality');

    // Clear any module success messages.
    drupal_get_messages('status');

    // Add configuration forms for optional deps if defined.
    $optionalDeps = $this->optionalDependencyManager->getDefinitions();
    $form['optional_dependencies'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Optional Dependencies'),
      '#collapsible' => FALSE,
      '#tree' => TRUE,
    ];

    $hasOptionalDepsForm = FALSE;
    $form_state->set('optional_dependencies', []);
    foreach ($optionalDeps as $optionalDep) {
      /** @var \Drupal\presto\Installer\OptionalDependencies\OptionalDependencyInterface $instance */
      $instance = $this->optionalDependencyManager->createInstance(
        $optionalDep['id']
      );

      $pluginForm = [];
      $subFormState = SubformState::createForSubform(
        $pluginForm,
        $form,
        $form_state
      );
      $form['optional_dependencies'][$optionalDep['id']] = $instance->buildConfigurationForm(
        $pluginForm,
        $subFormState
      );

      if (count($form['optional_dependencies'][$optionalDep['id']]) > 0) {
        $hasOptionalDepsForm = TRUE;
      }

      $form_state->set(['optional_dependencies', $optionalDep['id']], $instance);
    }

    // Hide optional dependencies fieldset if the config form is empty.
    $form['optional_dependencies']['#access'] = $hasOptionalDepsForm;

    $form['ecommerce'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('eCommerce'),
      '#collapsible' => FALSE,
    ];

    // Only enable commerce if this profile was installed via Composer
    // (which is when the below interface will exist).
    $enableCommerce = FALSE;
    /** @noinspection ClassConstantCanBeUsedInspection */
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

    // Submit any plugin forms.
    $install_state[0][OptionalDependenciesInstaller::CONFIG_KEY] = [];
    $depConfig =& $install_state[0][OptionalDependenciesInstaller::CONFIG_KEY];
    foreach (Element::children($form['optional_dependencies']) as $dependencyId) {
      /** @var \Drupal\presto\Installer\OptionalDependencies\OptionalDependencyInterface $instance */
      /** @noinspection ReferenceMismatchInspection */
      $instance = $form_state->get(['optional_dependencies', $dependencyId]);

      $subFormState = SubformState::createForSubform(
        $form['optional_dependencies'][$dependencyId],
        $form,
        $form_state
      );

      $instance->submitConfigurationForm($form, $subFormState);
      $depConfig[$dependencyId] = $instance->getConfiguration();
    }

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
