<?php

namespace Drupal\presto\Installer\Ecommerce;

use Drupal;
use Drupal\presto\Installer\Ecommerce\Content\Manager as DemoContentManager;

/**
 * Presto eCommerce module + content installer.
 *
 * @package Drupal\presto\Installer\Ecommerce
 */
class Installer {

  const DEPENDENCY_TYPE_MODULE = 'module';
  const DEPENDENCY_TYPE_THEME = 'theme';

  /**
   * All eCommerce dependencies.
   *
   * @var array
   */
  private $dependencies = [
    'commerce' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_order' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_price' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_product' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_cart' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_checkout' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_payment' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_payment_example' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_promotion' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_tax' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_log' => self::DEPENDENCY_TYPE_MODULE,
    'physical' => self::DEPENDENCY_TYPE_MODULE,
    'commerce_shipping' => self::DEPENDENCY_TYPE_MODULE,
  ];

  /**
   * Current install state.
   *
   * @var array
   */
  private $installState;

  /**
   * The demo content creation manager.
   *
   * @var \Drupal\presto\Installer\Ecommerce\Content\Manager
   */
  private $demoContentManager;

  /**
   * PrestoEcommerceInstaller constructor.
   *
   * @param array $installState
   *   Current install state.
   * @param \Drupal\presto\Installer\Ecommerce\Content\Manager $manager
   *   The demo content creation manager.
   */
  public function __construct(
    array $installState,
    DemoContentManager $manager
  ) {
    $this->installState = $installState;
    $this->demoContentManager = $manager;
  }

  /**
   * Creates a new instance of this class.
   *
   * @param array $installState
   *   Current install state.
   *
   * @return static
   */
  public static function create(array $installState) {
    $demoContentManager = Drupal::service(
      'plugin.manager.presto.ecommerce_demo_content'
    );
    return new static($installState, $demoContentManager);
  }

  /**
   * Sets up all install tasks if they're enabled.
   *
   * @return array
   *   A batch operations definition with all enabled install tasks.
   */
  public function installIfEnabled() {
    $operations = [];

    // Attempt to install modules if we can.
    if ($this->shouldInstallModules()) {
      $operations = array_merge($operations, $this->addDependencyOperations());
    }

    if ($this->shouldInstallDemoContent()) {
      $operations = array_merge($operations, $this->addDemoContentOperations());
    }

    return $operations;
  }

  /**
   * Check if this installer is allowed to install eCommerce modules.
   *
   * @return bool
   *   TRUE if allowed, FALSE otherwise.
   */
  private function shouldInstallModules() {
    return (bool) $this->installState['presto_ecommerce_enabled'];
  }

  /**
   * Check if we should create demo content too.
   *
   * @return bool
   *   TRUE if allowed, FALSE otherwise.
   */
  private function shouldInstallDemoContent() {
    $create = (bool) $this->installState['presto_ecommerce_install_demo_content'];
    return $this->shouldInstallModules() && $create;
  }

  /**
   * Crates a set of batch operations to install all required dependencies.
   *
   * @return array
   *   A set of Drupal batch operations.
   */
  private function addDependencyOperations() {
    $operations = [];

    foreach ($this->dependencies as $module => $type) {
      $operations[] = [
        [static::class, 'installDependency'],
        [
          $module,
          $type,
        ],
      ];
    }

    return $operations;
  }

  /**
   * Crates a set of batch operations to create demo content.
   *
   * @return array
   *   A set of Drupal batch operations.
   */
  private function addDemoContentOperations() {
    $operations = [];

    $contentDefs = $this->demoContentManager->getDefinitions();

    // Add module install task to install our commerce exports module.
    $operations[] = [
      [static::class, 'installDependency'],
      [
        'presto_commerce',
        static::DEPENDENCY_TYPE_MODULE,
      ],
    ];

    // Run any further content tasks.
    foreach ($contentDefs as $def) {
      $operations[] = [
        [static::class, 'createDemoContent'],
        [$def['id']],
      ];
    }

    return $operations;
  }

  /**
   * Installs a Drupal dependency (e.g. a module or a theme).
   *
   * This is a Drupal batch callback operation and as such, needs to be both a
   * public and a static function so that the Batch API can access it outside
   * the context of this class.
   *
   * @param string $dependency
   *   Dependency machine name.
   * @param string $type
   *   Dependency type.
   * @param array $context
   *   Batch context.
   *
   * @throws Drupal\Core\Extension\ExtensionNameLengthException
   * @throws Drupal\Core\Extension\MissingDependencyException
   * @throws InstallerException
   */
  public static function installDependency($dependency, $type, array &$context) {
    // Reset time limit so we don't timeout.
    drupal_set_time_limit(0);

    switch ($type) {
      case static::DEPENDENCY_TYPE_MODULE:
        /** @var \Drupal\Core\Extension\ModuleInstaller $moduleInstaller */
        $moduleInstaller = Drupal::service('module_installer');
        $moduleInstaller->install([$dependency], TRUE);
        break;

      case static::DEPENDENCY_TYPE_THEME:
        /** @var \Drupal\Core\Extension\ThemeInstaller $themeInstaller */
        $themeInstaller = Drupal::service('theme_installer');
        $themeInstaller->install([$dependency], TRUE);
        break;

      default:
        throw new InstallerException(
          "Unknown dependency type '{$type}'."
        );
    }

    $context['results'][] = $dependency;
    $context['message'] = t(
      'Installed @dependency_type %dependency.',
      [
        '@dependency_type' => $type,
        '%dependency' => $dependency,
      ]
    );
  }

  /**
   * Creates a demo content item.
   *
   * This is a Drupal batch callback operation and as such, needs to be both a
   * public and a static function so that the Batch API can access it outside
   * the context of this class.
   *
   * @param string $pluginId
   *   Demo content class plugin ID.
   * @param array $context
   *   Batch context.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public static function createDemoContent($pluginId, array &$context) {
    // Reset time limit so we don't timeout.
    drupal_set_time_limit(0);

    // Needs to be resolved manually since we don't have a context.
    /** @var \Drupal\presto\Installer\Ecommerce\Content\Manager $demoContentManager */
    $demoContentManager = Drupal::service(
      'plugin.manager.presto.ecommerce_demo_content'
    );

    $definition = $demoContentManager->getDefinition($pluginId);
    /** @var \Drupal\Core\StringTranslation\TranslatableMarkup $label */
    $label = $definition['label'];

    /** @var \Drupal\presto\Installer\Ecommerce\Content\Plugin\AbstractDemoContent $instance */
    $instance = $demoContentManager->createInstance($pluginId);
    $instance->createContent();

    $context['results'][] = $pluginId;
    $context['message'] = t('Running %task_name', [
      '%task_name' => lcfirst($label->render()),
    ]);
  }

}
